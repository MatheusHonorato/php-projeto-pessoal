<?php

declare(strict_types=1);

namespace App\DB;

interface QueryBuilderInterface
{
    public function table(string $table): QueryBuilder;

    public function find(
        ?array $terms = null,
        ?array $params = null,
        string $columns = '*',
    ): QueryBuilder;

    public function findById(int $id): QueryBuilder;

    public function fetch(): QueryBuilder;

    public function create(array $data, string $table = ''): QueryBuilder;

    public function update(array $data, string $table = ''): QueryBuilder;

    public function delete(string $terms, array $params, string $table = ''): QueryBuilder;

    public function execute(): int|bool;

    public function join(string $table_join, array $keys): QueryBuilder;

    public function getResult(int $limit = 0, int $offset = 0): array;
}
