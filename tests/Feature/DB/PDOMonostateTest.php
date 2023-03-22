<?php

declare(strict_types=1);

namespace tests\Feature\DB;

use App\config\DBConfig;
use App\DB\PDOMonostate;
use PDO;
use PHPUnit\Framework\TestCase;

class PDOMonostateTest extends TestCase
{
  public function testCheckConnectionPDOMonostateTypeObjectReturn(): void 
  {
    $this->assertEquals(expected: "object", actual: gettype((new PDOMonostate(config: DBConfig::getConfig()))->getConnection())); 
  }

  public function testCheckConnectionPDOMonostateTypePDOInstanceReturn(): void
  {
    $this->assertTrue(condition: (new PDOMonostate(config: DBConfig::getConfig()))->getConnection() instanceof PDO); 
  }
}

