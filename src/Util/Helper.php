<?php

declare(strict_types=1);

namespace App\Util;

use stdClass;

class Helper
{
    public static function validatedPaginate(stdClass $requestValidated): array
    {
        ['limit' => $limit, 'offset' => $offset] = (array) $requestValidated;

        unset($requestValidated->limit, $requestValidated->offset);

        return [(array) $requestValidated, (int) $limit, (int) $offset];
    }

    public static function methodObjectCall(Container $container, string $object, string $action = "", array $params = []): mixed
    {
        if (class_exists(class: $object)) {
            $newObject = $container->make($object);

            if (method_exists(object_or_class: $object, method: $action)) {
                $params = array_map(
                    fn ($value) => self::castingNumericValues($value),
                    $params
                );

                return $newObject->$action(...$params);
            }

            return $newObject;
        }
    }

    public static function castingNumericValues(mixed $value): mixed
    {
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                return floatval($value);
            }
            return intval($value);
        }
        return $value;
    }
}
