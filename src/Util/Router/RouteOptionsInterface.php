<?php

namespace App\Util\Router;

interface RouteOptionsInterface
{
  public function optionExist(string $index): bool;

  public function execute(string $index): array;
}
