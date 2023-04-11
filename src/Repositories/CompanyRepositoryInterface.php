<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CompanyModelAbstract;

interface CompanyRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): array;

    public function finByParam(array $terms, int $limit, int $offset): array;

    public function getAll(int $limit, int $offset): array;

    public function save(CompanyModelAbstract $company, array $user_ids): array;

    public function update(CompanyModelAbstract $company, array $user_ids): array|bool;

    public function destroy(int $id): array|bool;
}