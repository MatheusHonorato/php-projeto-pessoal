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
        private ?array $extra_datas = ['id' => null]
    ) {
    }

    public function setExtraDatas(array $value): RequestModelCompany
    {
        $this->extra_datas = $value;

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
            extra_datas: $this->extra_datas,
        );

        if (!array_key_exists('id', $validated)) {
            return ['errors' => $validated];
        }

        return new $this->model(...$validated);
    }
}
