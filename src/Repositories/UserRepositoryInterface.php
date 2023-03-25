<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserModelAbstract;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): array;

    public function finByParam(array $terms, int $limit, int $offset): array;

    public function getAll(int $limit, int $offset): array;

    public function save(UserModelAbstract  $user, array $company_ids): array;

    public function update(UserModelAbstract $user, array $company_ids): array | bool;

    public function destroy(int $id): array | bool;
}