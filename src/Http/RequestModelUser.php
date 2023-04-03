<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\UserModel;
use App\Util\HttpInterface;
use App\Util\ValidatorInterface;

class RequestModelUser implements RequestModelUserInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private string $model,
        private string $unique = 'unique:user',
        private ?array $extraDatas = ['id' => null]
    ) {
    }

    public function setExtraDatas(array $value): RequestModelUser
    {
        $this->extraDatas = $value;

        return $this;
    }

    public function setUnique(string $value): RequestModelUser
    {
        $this->unique = $value;

        return $this;
    }

    public function validated(HttpInterface $http): UserModel|array
    {
        $validated = (array) $http->request::validate(
            validator: $this->validator,
            params: [
                'name' => ['required', 'string'],
                'email' => ['required', $this->unique],
                'phone' => ['required', 'string'],
                'date' => ['required', 'date'],
                'city' => ['required', 'string'],
                'company_ids' => ['required', 'uniqueArray', 'foreignKey:company:id'],
            ],
            extraDatas: $this->extraDatas,
        );

        if (!array_key_exists(key: 'id', array: $validated)) {
            return ['errors' => $validated];
        }

        return new $this->model(...$validated);
    }
}
