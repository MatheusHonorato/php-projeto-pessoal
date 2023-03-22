<?php

declare(strict_types=1);

namespace App\Controllers;

use App\config\DBConfig;
use App\DB\PDOMonostate;
use App\DB\QueryBuilder;
use App\Http\{Request, RequestModelCompany};
use App\Models\CompanyModel;
use App\Repositories\CompanyRepository;
use App\Util\{Response, Validator};
use stdClass;

class CompaniesController implements ControllerInterface
{
    private static CompanyRepository $companyRepository;

    public function __construct()
    {
        self::$companyRepository = new CompanyRepository(
            queryBuilder: new QueryBuilder(
                db: new PDOMonostate(
                    config: DBConfig::getConfig()
                )
            )
        );
    }

    public function get(?string $id = null): stdClass
    {
        $extra_datas = ['limit' => null, 'offset' => null];

        $request_user = Request::validate(extra_datas: $extra_datas);

        $terms = (array) $_GET;

        if(!is_numeric($id) && $id != null) {
            return Response::execute(data: [], status: 200);
        }

        if($id) {
            return Response::execute(data: self::$companyRepository->findById(id: (int) $id), status: 200);
        }

        if(count($terms) > 0) {
            return Response::execute(data: self::$companyRepository->finByParam(
                terms: $terms,
                limit: (int) $request_user->limit,
                offset: (int) $request_user->offset),
                status: 200
            );
        } 

        return Response::execute(data: self::$companyRepository->getAll(
            limit: (int) $request_user->limit,
            offset: (int) $request_user->offset),
            status: 200
        );
    }

    public function post(): stdClass
    {
        $request_company = RequestModelCompany::validated(
            request: new Request,
            validator: new Validator,
            unique: 'unique:company',
        );           
        
        try {
            $company =  new CompanyModel(...(array) $request_company);
        } catch (\Throwable $th) {
            return Response::execute(data: (array) $request_company, status: 500);
        }

        return Response::execute(data: self::$companyRepository->save(company: $company, user_ids: $company->user_ids), status: 201);
    }

    public function put(string $id): stdClass
    {
        $request_company = RequestModelCompany::validated(
            request: new Request,
            validator: new Validator,
            extra_datas: ['id' => (int) $id],
            unique: 'uniqueIgnoreThis:company',
        );

        try {
            $company =  new CompanyModel(...(array) $request_company);
        } catch (\Throwable) {
            return Response::execute(data: (array) $request_company, status: 500);
        }

        return Response::execute(data: self::$companyRepository->update(company: $company, user_ids: $company->user_ids), status: 200);
    }

    public function delete(string $id = null): stdClass
    {    
        if(self::$companyRepository->destroy(company_id: (int) $id) === true) {
            return Response::execute(data: [], status: 204);
        }
        return Response::execute(data: [], status: 404);
    }
}