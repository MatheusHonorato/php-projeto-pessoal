<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DB\QueryBuilderInterface;
use App\Models\{CompanyModelAbstract, ModelInterface, UserCompanyModelAbstract};

class CompanyRepository implements CompanyRepositoryInterface
{
  const FIRST = 0;
  public function __construct(private QueryBuilderInterface $queryBuilder){}


  public function findById(int $id): array
  {
      $company = $this->queryBuilder->table(CompanyModelAbstract::TABLE)
                    ->findById(id: $id)
                    ->getResult();

      $users = $this->queryBuilder->table(UserCompanyModelAbstract::TABLE)
                    ->find(terms: ['company_id' => $id], columns: "users.*")
                    ->join(table_join: 'users', keys: ['users.id', 'users_companies.user_id'])
                    ->getResult();
      
      $company ? $company[self::FIRST]['users'] = $users : '';

        if(count($company) > 0) {
            return $company[self::FIRST];
        }
        return $company;
    }

    public function finByParam(array $terms, int $limit, int $offset): array
    {
        $companies = $this->queryBuilder->table(CompanyModelAbstract::TABLE);

        if(isset($terms['user'])) {
            $companies = $companies
                        ->find(terms: ['users.name' => $terms['user']], columns: "companies.*")
                        ->join(table_join: 'users_companies', keys: ['companies.id', 'users_companies.company_id'])
                        ->join(table_join: 'users', keys: ['users.id', 'users_companies.user_id'])
                        ->getResult(limit: $limit, offset: $offset);
        }
        else {
            $companies = $companies->find(terms: $terms)->getResult(limit: $limit, offset: $offset);
        }

        foreach ($companies as $key => $company) {
            $companies[$key]['users'] = $this->queryBuilder->table(UserCompanyModelAbstract::TABLE)
                                            ->find(terms: ['company_id' => $company['id']], columns: "users.*")
                                            ->join(table_join: 'users', keys: ['users.id', 'users_companies.user_id'])
                                            ->getResult();
        }


        return $companies;
    }

    public function getAll(int $limit, int $offset): array
    {
        $companies = $this->queryBuilder->table(CompanyModelAbstract::TABLE)->fetch()->getResult(limit: $limit, offset: $offset);

        foreach ($companies as $key => $company) {
            $companies[$key]['users'] = $this->queryBuilder->table(UserCompanyModelAbstract::TABLE)
                                            ->find(terms: ['company_id' => $company['id']], columns: "users.*")
                                            ->join(table_join: 'users', keys: ['users.id', 'users_companies.user_id'])
                                            ->getResult();   
        }

        return $companies;
    }

    public function save(ModelInterface $company, array $user_ids): array
    {
        $new_company = $this->queryBuilder->table(CompanyModelAbstract::TABLE)->create(data: $company->toArray(), table: "");

        foreach ($user_ids as $user_id) {
            $new_company->create(data: ['user_id' => $user_id, 'company_id' => false], table: 'users_companies');
        }

        $id = $new_company->execute();

        return $this->findById($id);
    }

    public function update(ModelInterface $company, array $user_ids): array | bool
    {

        $update_company = $this->queryBuilder->table(CompanyModelAbstract::TABLE)
                            ->update(data: $company->toArray())
                            ->delete(table: "users_companies", terms: "company_id = :company_id", params: ['company_id' => (string) $company->id]);

        foreach ($user_ids as $user_id) {
            $update_company->create(data: ['user_id' => $user_id, 'company_id' => $company->id], table: "users_companies");
        }

        if($update_company->execute() === false) {
            return ["user does not exist."];
        }

        return $this->findById($company->id);
    }

    public function destroy(int $id): array | bool
    {
        $update_company = $this->queryBuilder->table(UserCompanyModelAbstract::TABLE)
            ->delete(table: "", terms: "company_id = :company_id", params: ["company_id" => $id])
            ->delete(table: "companies", terms: "id = :id", params: ["id" => (string) $id]);

        if($update_company->execute() === false) {
            return ["company does not exist."];
        }

        return true;
    }
}