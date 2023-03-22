<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CompanyModel;

interface CompanyRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): array;

    public function finByParam(array $terms, int $limit, int $offset): array;

    public function getAll(int $limit, int $offset): array;

    public function save(CompanyModel $company, array $user_ids): array;

    public function update(CompanyModel $company, array $user_ids): array | bool;

    public function destroy(int $company_id): array | bool;
}