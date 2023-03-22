<?php

declare(strict_types=1);

namespace App\DB;

interface DBInterface
{
    public function __construct(object $config);

    public function getConnection (): object;
}