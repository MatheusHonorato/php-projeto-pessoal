<?php

declare(strict_types=1);

namespace App\DB;

class QueryBuilder implements QueryBuilderInterface
{
    public const FIRST = 0;
    private string $query;
    private string $join;
    private array $queries_execute = [];
    private array $terms = [];
    private bool $flag_like = false;
    private string $table;

    public function __construct(
        private DBInterface $db,
    ) {
        $this->query = '';
        $this->join = '';
    }

    public function table(string $table): QueryBuilder
    {
        $this->table = $table;

        return $this;
    }

    public function find(
        ?array $terms = null,
        ?array $params = null,
        string $columns = '*',
    ): QueryBuilder {
        $this->terms = $terms;

        $and = ' or ';
        $terms_query = '';

        $index = 1;
        if (null == $params) {
            $params = $this->terms;
            foreach ($params as $key => $value) {
                ($index >= count($params)) ? $and = '' : false;

                ($index >= count($params)) ? $or = '' : false;

                if (is_array($value)) {
                    $this->terms[$key] = implode(', ', $value);
                    $terms_query = $terms_query."{$key} IN ({$this->terms[$key]}){$and} ";
                    unset($terms[$key]);
                } elseif (false !== strpos($key, 'id')) {
                    $terms_query = $terms_query."{$key} = :{$key}{$and} ";
                } else {
                    $terms_query = $terms_query."{$key} LIKE '%{$value}%{$or}' ";
                    $this->flag_like = true;
                    unset($this->terms[$key]);
                }

                ++$index;
            }
        }

        $this->query = "SELECT DISTINCT {$columns} FROM {$this->table} WHERE {$terms_query}";

        return $this;
    }

    public function findById(int $id): QueryBuilder
    {
        return $this->find(terms: ['id' => $id]);
    }

    public function fetch(): QueryBuilder
    {
        $this->query = "SELECT * FROM {$this->table}";

        return $this;
    }

    public function create(array $data, string $table = ''): QueryBuilder
    {
        '' == $table ? $table = $this->table : '';

        $columns = implode(', ', array_keys($data));
        $values = ':'.implode(', :', array_keys($data));
        $this->queries_execute[] = ['query' => "INSERT INTO {$table} ({$columns}) VALUES ({$values})", 'params' => $data];

        return $this;
    }

    public function update(array $data, string $table = ''): QueryBuilder
    {
        '' == $table ? $table = $this->table : '';

        $columns = '';
        $comma = ',';

        $index = 1;
        foreach ($data as $key => $value) {
            ($index >= count($data)) ? $comma = '' : false;
            ('id' != $key) ? $columns = $columns."{$key} = :{$key}{$comma} " : false;
            ++$index;
        }

        $this->queries_execute[] = ['query' => "UPDATE {$table} SET {$columns} WHERE id = :id", 'params' => $data];

        return $this;
    }

    public function delete(string $terms, array $params, string $table = ''): QueryBuilder
    {
        '' == $table ? $table = $this->table : '';

        $this->queries_execute[] = ['query' => "DELETE FROM {$table} WHERE {$terms}", 'params' => $params];

        return $this;
    }

    public function execute(): int|bool
    {
        $last_insert_id = 0;
        $count = 0;

        try {
            $this->db->getConnection()->beginTransaction();

            foreach ($this->queries_execute as $query_execute) {
                if ($query_execute['params']) {
                    foreach ($query_execute['params'] as $key => $value) {
                        if (false == $value) {
                            $query_execute['params'][$key] = $last_insert_id;
                        }
                    }
                }

                $stmt = $this->db->getConnection()->prepare($query_execute['query']);

                $stmt->execute($query_execute['params']);

                (0 == $count) ? $last_insert_id = $this->db->getConnection()->lastInsertId() : '';
                ++$count;
            }

            $this->db->getConnection()->commit();
        } catch (\Exception) {
            return false;
        }

        if (0 == $stmt->rowCount()) {
            return false;
        }

        return (int) $last_insert_id;
    }

    public function join(string $table_join, array $keys): QueryBuilder
    {
        $this->join = $this->join." JOIN $table_join ON $keys[0] = $keys[1] ";

        return $this;
    }

    public function getResult(int $limit = 0, int $offset = 0): array
    {
        $query_limit = '';

        (0 != $limit && 0 != $offset) ? $query_limit = " LIMIT $limit OFFSET $offset" : '';

        if ('' != $this->join && null != $this->join && strpos($this->query, 'WHERE')) {
            $this->query = str_replace('WHERE', "$this->join WHERE", $this->query);
        }

        $stmt = $this->db->getConnection()->prepare($this->query.$query_limit);

        if ($this->flag_like) {
            try {
                $stmt->execute();
            } catch (\Throwable) {
            }
        }

        if (!$this->flag_like) {
            try {
                $stmt->execute($this->terms);
            } catch (\Throwable $th) {
            }
        }

        $this->query = '';
        $this->join = '';
        $this->queries_execute = [];
        $this->terms = [];
        $this->flag_like = false;

        if (1 === $stmt->rowCount()) {
            return [$stmt->fetch($this->db->getConnection()::FETCH_ASSOC)];
        }

        return $stmt->fetchAll($this->db->getConnection()::FETCH_ASSOC);
    }
}
