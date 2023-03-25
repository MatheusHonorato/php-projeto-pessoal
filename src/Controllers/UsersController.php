<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\{RequestModelUserInterface};
use App\Repositories\UserRepositoryInterface;
use App\Util\{HttpInterface};
use stdClass;

class UsersController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RequestModelUserInterface $requestModelUser,
    ){}

    public function index(HttpInterface $http): object
    {
        $requestValidated = $http->request->validate();

        ['limit' => $limit, 'offset' => $offset] = (array) $requestValidated;

        unset($requestValidated->limit, $requestValidated->offset);
    
        if (count((array) $requestValidated) > 0) {
            return  $http->response->execute(
                data: $this->userRepository->finByParam(
                    terms: (array) $requestValidated, 
                    limit: (int) $limit, 
                    offset: (int) $offset
                ),
                status: 200
            );
        }

        return $http->response->execute(
            data: $this->userRepository->getAll(
                limit: (int) $limit,
                offset: (int) $offset
            ),
            status: 200
        );
    }

    public function getById($param, HttpInterface $http): object
    {
        if (!is_numeric($param['id']) && $param['id'] != null) {
            return $http->response->execute(data: [], status: 200);
        }

        return $http->response->execute(data: $this->userRepository->findById(id: (int) $param['id']), status: 200);
    }

    public function store(
        HttpInterface $http,
    ): stdClass
    {
        try {
            $user = $this->requestModelUser->validated(http: $http);

            if (is_array($user) && isset($user['errors'])) {
                return $http->response->execute(data: $user['errors'], status: 500);
            }

        } catch (\Throwable) {
            return $http->response->execute(data: (array) $user, status: 500);
        }
        
        return $http->response->execute(data: $this->userRepository->save(user: $user, company_ids: $user->company_ids), status: 201);
    }

    public function update($param, HttpInterface $http): stdClass
    {      
        try {
            $user = $this->requestModelUser
                        ->setExtraDatas($param)
                        ->setUnique('uniqueIgnoreThis:user')
                        ->validated(http: $http);

            if (is_array($user) && isset($user['errors'])) {
                return $http->response->execute(data: $user['errors'], status: 500);
            }

        } catch (\Throwable) {
            return $http->response->execute(data: (array) $user, status: 500);
        }

        return $http->response->execute(data: $this->userRepository->update(user: $user, company_ids: $user->company_ids), status: 200);
    }

    public function destroy(array $param, HttpInterface $http): stdClass
    {   
        if ($this->userRepository->destroy(id: (int) $param['id']) === true) {
            return $http->response->execute(data: [], status: 204);
        }

        return $http->response->execute(data: [], status: 404);
    }
}