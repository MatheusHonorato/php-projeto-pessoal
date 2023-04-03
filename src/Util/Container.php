<?php

declare(strict_types=1);

namespace App\Util;

class Container
{
    private static Container|null $instance = null;

    protected array $bindings = [];

    public static function instance(): Container
    {
        if (is_null(value: self::$instance)) {
            self::$instance = new Container();
        }

        return self::$instance;
    }

    public function bind(mixed $key, mixed $value): void
    {
        $this->bindings[$key] = $value;
    }

    public function make(mixed $key): mixed
    {
        if (!isset($this->bindings[$key]) || !is_callable(value: $this->bindings[$key])) {
            return $this->bindings[$key];
        }

        return call_user_func($this->bindings[$key]);
    }
}
