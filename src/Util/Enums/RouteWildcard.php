<?php

namespace App\Util\Enums;

enum RouteWildcard: string
{
    case NUMERIC = '[0-9]+';
    case alpha = '[a-z]+';
    case any = '[a-z0-9\-]+';
}
