<?php

declare(strict_types=1);

namespace App\Controllers;

use stdClass;

interface ControllerInterface
{
    public function get(?string $id = null): stdClass;

    public function post(): stdClass;

    public function put(string $id): stdClass;

    public function delete(string $id = null): stdClass;
}