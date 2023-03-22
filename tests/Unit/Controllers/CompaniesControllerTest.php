<?php

declare(strict_types=1);

namespace tests\Unit\DB;

use App\Controllers\CompaniesController;
use PHPUnit\Framework\TestCase;

class CompaniesControllerTest extends TestCase
{
  public function testGetMethodParamterIdNullReturnTypeArray(): void 
  {
    $company_controller = new CompaniesController();

    $this->assertEquals(expected: "array", actual: gettype($company_controller->get())); 
  }

}

