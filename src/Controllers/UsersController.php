<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\{RequestModelUserInterface};
use App\Repositories\UserRepositoryInterface;
use App\Util\{Helper, HttpInterface};

class UsersController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RequestModelUserInterface $requestModelUser,
    ) {
    }

    public function index(HttpInterface $http): object
    {
        [$requestValidated, $limit, $offset] = Helper::validatedPaginate($http->request->validate());

        if (count($requestValidated) > 0) {
            return $http->response->execute(
                data: $this->userRepository->finByParam(
                    terms: $requestValidated,
                    limit: $limit,
                    offset: $offset
                ),
                status: 200
            );
        }

        return $http->response->execute(
            data: $this->userRepository->getAll(
                limit: $limit,
                offset: $offset
            ),
            status: 200
        );
    }

    public function getById(int $id, HttpInterface $http): object
    {
        return $http->response->execute(data: $this->userRepository->findById(id: $id), status: 200);
    }

    public function store(
        HttpInterface $http,
    ): \stdClass {
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

    public function update(int $id, HttpInterface $http): \stdClass
    {
        try {
            $user = $this->requestModelUser
                ->setExtraDatas(['id' => $id])
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

    public function destroy(int $id, HttpInterface $http): \stdClass
    {
        if (true === $this->userRepository->destroy(id: $id)) {
            return $http->response->execute(data: ['Success'], status: 204);
        }

        return $http->response->execute(data: [], status: 404);
    }
}
