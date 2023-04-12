<?php

declare(strict_types=1);

namespace App\DB;

class QueryBuilder implements QueryBuilderInterface
{
    public const FIRST = 0;
    private string $query;
    private string $join;
    private array $queriesExecute = [];
    private array $terms = [];
    private bool $flagLike = false;
    private string $table;
    public const ORIGIN_TABLE = 0;
    public const DESTINY_TABLE = 1;

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
        $termsQuery = '';

        $index = 1;
        if (null == $params) {
            $params = $this->terms;
            foreach ($params as $key => $value) {
                ($index >= count($params)) ? $and = '' : false;

                ($index >= count($params)) ? $or = '' : false;

                if (is_array($value)) {
                    $this->terms[$key] = implode(', ', $value);
                    $termsQuery = $termsQuery."{$key} IN ({$this->terms[$key]}){$and} ";
                    unset($terms[$key]);
                } elseif (false !== strpos($key, 'id')) {
                    $termsQuery = $termsQuery."{$key} = :{$key}{$and} ";
                } else {
                    $termsQuery = $termsQuery."{$key} LIKE '%{$value}%{$or}' ";
                    $this->flagLike = true;
                    unset($this->terms[$key]);
                }

                ++$index;
            }
        }

        $this->query = "SELECT DISTINCT {$columns} FROM {$this->table} WHERE {$termsQuery}";

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
        $this->queriesExecute[] = ['query' => "INSERT INTO {$table} ({$columns}) VALUES ({$values})", 'params' => $data];

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

        $this->queriesExecute[] = ['query' => "UPDATE {$table} SET {$columns} WHERE id = :id", 'params' => $data];

        return $this;
    }

    public function delete(string $terms, array $params, string $table = ''): QueryBuilder
    {
        '' == $table ? $table = $this->table : '';

        $this->queriesExecute[] = ['query' => "DELETE FROM {$table} WHERE {$terms}", 'params' => $params];

        return $this;
    }

    public function execute(): int|bool
    {
        $lastInsertId = 0;
        $count = 0;

        try {
            $this->db->getConnection()->beginTransaction();

            foreach ($this->queriesExecute as $queryExecute) {
                if ($queryExecute['params']) {
                    foreach ($queryExecute['params'] as $key => $value) {
                        if (false == $value) {
                            $queryExecute['params'][$key] = $lastInsertId;
                        }
                    }
                }

                $stmt = $this->db->getConnection()->prepare($queryExecute['query']);

                $stmt->execute($queryExecute['params']);

                (0 == $count) ? $lastInsertId = $this->db->getConnection()->lastInsertId() : '';
                ++$count;
            }

            $this->db->getConnection()->commit();
        } catch (\Exception) {
            return false;
        }

        if ($stmt->rowCount() == 1 && $lastInsertId == 0) {
            return true;
        }

        if (0 == $stmt->rowCount()) {
            return false;
        }

        return (int) $lastInsertId;
    }

    public function join(string $tableJoin, array $keys): QueryBuilder
    {
        $this->join = $this->join." JOIN $tableJoin ON ".$keys[self::ORIGIN_TABLE]." = ".$keys[self::DESTINY_TABLE];

        return $this;
    }

    public function getResult(int $limit = 0, int $offset = 0): array
    {
        $queryLimit = '';

        (0 != $limit && 0 != $offset) ? $queryLimit = " LIMIT $limit OFFSET $offset" : '';

        if ('' != $this->join && null != $this->join && strpos($this->query, 'WHERE')) {
            $this->query = str_replace('WHERE', "$this->join WHERE", $this->query);
        }

        $stmt = $this->db->getConnection()->prepare($this->query.$queryLimit);

        if ($this->flagLike) {
            try {
                $stmt->execute();
            } catch (\Throwable) {
            }
        }

        if (!$this->flagLike) {
            try {
                $stmt->execute($this->terms);
            } catch (\Throwable) {
            }
        }

        $this->reset();

        if (1 === $stmt->rowCount()) {
            return [$stmt->fetch($this->db->getConnection()::FETCH_ASSOC)];
        }

        return $stmt->fetchAll($this->db->getConnection()::FETCH_ASSOC);
    }

    private function reset(): void
    {
        $this->query = '';
        $this->join = '';
        $this->queriesExecute = [];
        $this->terms = [];
        $this->flagLike = false;
    }
}
