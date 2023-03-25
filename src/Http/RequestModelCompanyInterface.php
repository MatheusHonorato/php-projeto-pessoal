<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\CompanyModel;
use App\Util\HttpInterface;

interface RequestModelCompanyInterface extends RequestModelInterface
{
    public function setExtraDatas(array $value): RequestModelCompany;

    public function validated(HttpInterface $http): CompanyModel|array;
}
