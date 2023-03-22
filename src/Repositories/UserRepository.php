<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DB\QueryBuilder;
use App\Models\{UserCompanyModel, UserModel};

class UserRepository implements UserRepositoryInterface
{
    const FIRST = 0;
    private static QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        self::$queryBuilder = $queryBuilder;
    }

    public function findById(int $id): array
    {
        $user = self::$queryBuilder->table(table: UserModel::TABLE)
                ->findById(id: $id)
                ->getResult();

        $companies = self::$queryBuilder->table(table: UserCompanyModel::TABLE)
                        ->find(terms: ['user_id' => $id], columns: "companies.*")
                        ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                        ->getResult();
        
        $user ? $user[self::FIRST]['companies'] = $companies : '';

        if(count($user) > 0) {
            return $user[self::FIRST];
        }
        return $user;
    }

    public function finByParam(array $terms, int $limit, int $offset): array
    {
        $users = self::$queryBuilder->table(table: UserModel::TABLE);

        if(isset($terms['company'])) {
            $users = $users
                        ->find(terms: ['companies.name' => $terms['company']], columns: "users.*")
                        ->join(table_join: 'users_companies', keys: ['users.id', 'users_companies.user_id'])
                        ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                        ->getResult(limit: $limit, offset: $offset);
        }   
        else {
            $users = $users->find(terms: $terms)->getResult(limit: $limit, offset: $offset);
        }

        foreach ($users as $key => $user) {
             $users[$key]['companies'] = self::$queryBuilder->table(table: UserCompanyModel::TABLE)
                                                ->find(terms: ['user_id' => $user['id']], columns: "companies.*")
                                                ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                                                ->getResult();
        }

        return $users;
    }

    public function getAll(int $limit, int $offset): array
    {
        $users = self::$queryBuilder->table(table: UserModel::TABLE)->fetch()->getResult(limit: $limit, offset: $offset);
        
        foreach ($users as $key => $user) {
            $users[$key]['companies'] = self::$queryBuilder->table(table: UserCompanyModel::TABLE)
                                            ->find(terms: ['user_id' => $user['id']], columns: "companies.*")
                                            ->join(table_join: 'companies', keys: ['companies.id', 'users_companies.company_id'])
                                            ->getResult();  
        }
                

        return $users;
    }

    public function save(UserModel $user, array $company_ids): array
    {
        $new_user = self::$queryBuilder->table(table: UserModel::TABLE)->create(data: $user->toArray(), table: "");

        foreach ($company_ids as $company_id) {
            $new_user->create(data: ['user_id' => false, 'company_id' => $company_id], table: 'users_companies');
        }

        $id = $new_user->execute();

        return self::findById($id);
    }

    public function update(UserModel $user, array $company_ids): array | bool
    {
        $update_company = self::$queryBuilder->table(table: UserModel::TABLE)
                            ->update(data: $user->toArray())
                            ->delete(table: "users_companies", terms: "user_id = :user_id", params: ['user_id' => (string) $user->id]);

        foreach ($company_ids as $company_id) {
            $update_company->create(data: ['user_id' => $user->id, 'company_id' => $company_id], table: "users_companies");
        }

        if($update_company->execute() === false) {
            return ["user does not exist."];
        }

        return self::findById($user->id);
    }

    public function destroy(int $user_id): array | bool
    {
        $update_company = self::$queryBuilder->table(table: UserCompanyModel::TABLE)
            ->delete(table: "", terms: "user_id = :user_id", params: ["user_id" => $user_id])
            ->delete(table: "users", terms: "id = :id", params: ["id" => (string) $user_id]);

        if($update_company->execute() === false) {
            return ["user does not exist."];
        }

        return true;
    }
}