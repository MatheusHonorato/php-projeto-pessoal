<?php

namespace App\Util\Router;

interface RouteWildcardInterface
{
    public function paramsToArray(string $uri, string $wildcard, array $aliases): void;

    public function replaceWildcardWithPattern(string $uriToReplace): void;

    public function uriEqualToPattern($currentUri, $wildcardReplaced): int|false;

    public function getWildcardReplaced(): string;

    public function getParams(): array;
}
