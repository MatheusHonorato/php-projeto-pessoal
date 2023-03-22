<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserModel;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): array;

    public function finByParam(array $terms, int $limit, int $offset): array;

    public function getAll(int $limit, int $offset): array;

    public function save(UserModel $user, array $company_ids): array;

    public function update(UserModel $user, array $company_ids): array | bool;

    public function destroy(int $company_id): array | bool;
}