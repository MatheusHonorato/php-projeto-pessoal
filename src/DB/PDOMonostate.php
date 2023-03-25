<?php

declare(strict_types=1);

namespace App\DB;

class PDOMonostate implements DBInterface
{
    protected static object $connection;
    protected static object $config;

    public function __construct(object $config)
    {
        self::$config = $config;
    }

    public function getConnection(): object
    {
        if (empty(self::$connection)) {
            try {
                self::$connection = new \PDO(
                    self::$config->DB_CONNECTION.':host='.self::$config->DB_HOST.';dbname='.self::$config->DB_NAME,
                    self::$config->DB_USER,
                    self::$config->DB_PASSWORD
                );
            } catch (\PDOException $exception) {
                throw new \PDOException($exception->getMessage());
            }
        }

        return self::$connection;
    }
}
