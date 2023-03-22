<?php

declare(strict_types=1);

namespace App\Controllers;

use App\config\DBConfig;
use App\DB\PDOMonostate;
use App\DB\QueryBuilder;
use App\Http\{Request, RequestModelUser};
use App\Models\UserModel;
use App\Repositories\UserRepository;
use App\Util\{Response, Validator};
use stdClass;

class UsersController implements ControllerInterface
{
    private static UserRepository $userRepository;

    public function __construct()
    {
        self::$userRepository = new UserRepository(
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

        foreach ($extra_datas as $key => $value) {
            unset($terms[$key]);
        }

        if (!is_numeric($id) && $id != null) {
            return Response::execute(data: [], status: 200);
        }

        if($id) {
            return Response::execute(data: self::$userRepository->findById(id: (int) $id), status: 200);
        }

        if(count($terms) > 0) {
            return  Response::execute(data: self::$userRepository->finByParam(
                terms: $terms, 
                limit: (int) $request_user->limit, 
                offset: (int) $request_user->offset),
                status: 200
            );
        }

        return Response::execute(data: self::$userRepository->getAll(
            limit: (int) $request_user->limit,
            offset: (int) $request_user->offset),
            status: 200
        );
    }

    public function post(): stdClass
    {
        $request_user = RequestModelUser::validated(
            request: new Request,
            validator: new Validator,
            unique: 'unique:user'
        );

        try {
            $user =  new UserModel(...(array) $request_user);
        } catch (\Throwable $th) {
            return Response::execute(data: (array) $request_user, status: 500);
        }

        return Response::execute(data: self::$userRepository->save(user: $user, company_ids: $user->company_ids), status: 201);
    }

    public function put(string $id): stdClass
    {
        $request_user = RequestModelUser::validated(
            request: new Request,
            validator: new Validator,
            extra_datas: ['id' => (int) $id],
            unique: 'uniqueIgnoreThis:user',
        );

        try {
            $user =  new UserModel(...(array) $request_user);
        } catch (\Throwable $th) {
            return Response::execute(data: (array) $request_user, status: 500);
        }
        
        return Response::execute(data: self::$userRepository->update(user: $user, company_ids: $user->company_ids), status: 200);
    }

    public function delete(string $id = null): stdClass
    {   
        if(self::$userRepository->destroy(user_id: (int) $id) === true) {
            return Response::execute(data: [], status: 204);
        }
        return Response::execute(data: [], status: 404);
    }
}