<?php

declare(strict_types=1);

namespace App\Util;

interface ControllerCallInterface
{
  public static function generate(ClearStringInterface $clearStringInterface, array $url): string;
}