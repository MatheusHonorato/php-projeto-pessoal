<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\CompanyModel;
use App\Util\HttpInterface;
use App\Util\ValidatorInterface;

class RequestModelCompany implements RequestModelCompanyInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private string $model,
        private string $unique = 'unique:company',
        private ?array $extraDatas = ['id' => null]
    ) {
    }

    public function setExtraDatas(array $value): RequestModelCompany
    {
        $this->extraDatas = $value;

        return $this;
    }

    public function setUnique(string $value): RequestModelCompany
    {
        $this->unique = $value;

        return $this;
    }

    public function validated(HttpInterface $http): CompanyModel|array
    {
        $validated = (array) $http->request::validate(
            validator: $this->validator,
            params: [
                'name' => ['required', 'string'],
                'cnpj' => ['required', $this->unique],
                'address' => ['required', 'string'],
                'user_ids' => ['required', 'uniqueArray', 'foreignKey:user:id'],
            ],
            extraDatas: $this->extraDatas,
        );

        if (!array_key_exists(key: 'id', array: $validated)) {
            return ['errors' => $validated];
        }

        return new $this->model(...$validated);
    }
}
