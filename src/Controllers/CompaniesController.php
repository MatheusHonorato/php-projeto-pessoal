<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\{RequestModelCompanyInterface};
use App\Repositories\CompanyRepositoryInterface;
use App\Util\{HttpInterface};

class CompaniesController
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private RequestModelCompanyInterface $requestModelCompany,
    ) {
    }

    public function index(HttpInterface $http): object
    {
        $requestValidated = $http->request->validate();

        ['limit' => $limit, 'offset' => $offset] = (array) $requestValidated;

        unset($requestValidated->limit, $requestValidated->offset);

        if (count((array) $requestValidated) > 0) {
            return $http->response->execute(
                data: $this->companyRepository->finByParam(
                    terms: (array) $requestValidated,
                    limit: (int) $limit,
                    offset: (int) $offset
                ),
                status: 200
            );
        }

        return $http->response->execute(
            data: $this->companyRepository->getAll(
                limit: (int) $limit,
                offset: (int) $offset
            ),
            status: 200
        );
    }

    public function getById($param, HttpInterface $http): object
    {
        if (!is_numeric($param['id']) && null != $param['id']) {
            return $http->response->execute(data: [], status: 200);
        }

        return $http->response->execute(data: $this->companyRepository->findById(id: (int) $param['id']), status: 200);
    }

    public function store(
        HttpInterface $http,
    ): \stdClass {
        try {
            $company = $this->requestModelCompany->validated(http: $http);

            if (is_array($company) && isset($company['errors'])) {
                return $http->response->execute(data: $company['errors'], status: 500);
            }
        } catch (\Throwable $th) {
            return $http->response->execute(data: (array) $company, status: 500);
        }

        return $http->response->execute(data: $this->companyRepository->save(company: $company, user_ids: $company->user_ids), status: 201);
    }

    public function update($param, HttpInterface $http): \stdClass
    {
        try {
            $company = $this->requestModelCompany
                        ->setExtraDatas($param)
                        ->setUnique('uniqueIgnoreThis:company')
                        ->validated(http: $http);

            if (is_array($company) && isset($company['errors'])) {
                return $http->response->execute(data: $company['errors'], status: 500);
            }
        } catch (\Throwable) {
            return $http->response->execute(data: (array) $company, status: 500);
        }

        return $http->response->execute(data: $this->companyRepository->update(company: $company, user_ids: $company->user_ids), status: 200);
    }

    public function destroy(array $param, HttpInterface $http): \stdClass
    {
        if (true === $this->companyRepository->destroy(id: (int) $param['id'])) {
            return $http->response->execute(data: [], status: 204);
        }

        return $http->response->execute(data: [], status: 404);
    }
}
