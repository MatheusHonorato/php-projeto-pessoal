<?php

declare(strict_types=1);

namespace App\Repositories;

interface RepositoryInterface
{
    public function findById(int $id): array;

    public function finByParam(array $terms, int $limit, int $offset): array;

    public function getAll(int $limit, int $offset): array;

    public function destroy(int $model_id): array | bool;
}
