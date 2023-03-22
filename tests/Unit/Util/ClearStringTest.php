<?php

declare(strict_types=1);

namespace tests\Unit\Util;

use PHPUnit\Framework\TestCase;

use App\Util\ClearString;

class ClearStringTest extends TestCase {

  public function testClearBarStringCheck(): void {
    $expected = "";
    $atual = "/\\";
    $this->assertEquals($expected, ClearString::execute($atual)); 
  }
}

