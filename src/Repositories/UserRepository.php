<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DB\QueryBuilderInterface;
use App\Models\UserCompanyModelAbstract;
use App\Models\UserModelAbstract;

class UserRepository implements UserRepositoryInterface
{
    public const FIRST = 0;

    public function __construct(private QueryBuilderInterface $queryBuilder)
    {
    }

    public function findById(int $id): array
    {
        $user = $this->queryBuilder->table(table: UserModelAbstract::TABLE)
                ->findById(id: $id)
                ->getResult();

        $companies = $this->queryBuilder->table(table: UserCompanyModelAbstract::TABLE)
                        ->find(terms: ['user_id' => $id], columns: 'companies.*')
                        ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                        ->getResult();

        $user ? $user[self::FIRST]['companies'] = $companies : '';

        if (count($user) > 0) {
            return $user[self::FIRST];
        }

        return $user;
    }

    public function finByParam(array $terms, int $limit, int $offset): array
    {
        $users = $this->queryBuilder->table(table: UserModelAbstract::TABLE);

        if (isset($terms['company'])) {
            $users = $users
                        ->find(terms: ['companies.name' => $terms['company']], columns: 'users.*')
                        ->join(table_join: 'users_companies', keys: ['users.id', 'users_companies.user_id'])
                        ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                        ->getResult(limit: $limit, offset: $offset);
        } else {
            $users = $users->find(terms: $terms)->getResult(limit: $limit, offset: $offset);
        }

        foreach ($users as $key => $user) {
            $users[$key]['companies'] = $this->queryBuilder->table(table: UserCompanyModelAbstract::TABLE)
                                               ->find(terms: ['user_id' => $user['id']], columns: 'companies.*')
                                               ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                                               ->getResult();
        }

        return $users;
    }

    public function getAll(int $limit, int $offset): array
    {
        $users = $this->queryBuilder->table(table: UserModelAbstract::TABLE)->fetch()->getResult(limit: $limit, offset: $offset);

        foreach ($users as $key => $user) {
            $teste = $this->queryBuilder->table(table: UserCompanyModelAbstract::TABLE)
            ->find(terms: ['user_id' => $user['id']], columns: 'companies.*')
            ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
            ->getResult();

            $users[$key]['companies'] = $teste;
        }

        return $users;
    }

    public function save(UserModelAbstract $user, array $company_ids): array
    {
        $new_user = $this->queryBuilder->table(table: UserModelAbstract::TABLE)->create(data: $user->toArray(), table: '');

        foreach ($company_ids as $company_id) {
            $new_user->create(data: ['user_id' => false, 'company_id' => $company_id], table: 'users_companies');
        }

        $id = $new_user->execute();

        return $this->findById($id);
    }

    public function update(UserModelAbstract $user, array $company_ids): array|bool
    {
        $update_company = $this->queryBuilder->table(table: UserModelAbstract::TABLE)
                            ->update(data: $user->toArray())
                            ->delete(table: 'users_companies', terms: 'user_id = :user_id', params: ['user_id' => (string) $user->id]);

        foreach ($company_ids as $company_id) {
            $update_company->create(data: ['user_id' => $user->id, 'company_id' => $company_id], table: 'users_companies');
        }

        if (false === $update_company->execute()) {
            return ['user does not exist.'];
        }

        return $this->findById($user->id);
    }

    public function destroy(int $id): array|bool
    {
        $update_company = $this->queryBuilder->table(table: UserCompanyModelAbstract::TABLE)
            ->delete(table: '', terms: 'user_id = :user_id', params: ['user_id' => $id])
            ->delete(table: 'users', terms: 'id = :id', params: ['id' => (string) $id]);

        if (false === $update_company->execute()) {
            return ['user does not exist.'];
        }

        return true;
    }
}
