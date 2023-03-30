<?php

declare(strict_types=1);

namespace App\Util;

use App\DB\QueryBuilderInterface;

class Validator extends ValidatorAbstract
{
    public function __construct(
        private QueryBuilderInterface $queryBuilder,
        protected array $errors = [],
    ) {
    }

    public function validateRequired($value, string $fieldName): void
    {
        if (empty($value)) {
            $this->addError(error: "{$fieldName} is required");
        }
    }

    public function validateEmail($value, string $fieldName): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(error: "{$fieldName} is not a valid email address");
        }
    }

    public function validateUnique($value, string $fieldName): void
    {
        if ($this->find(model: $value->model, value: $value->value, fieldName: $fieldName) > 0) {
            $this->addError(error: "{$fieldName} unique is required");
        }
    }

    public function validateUniqueIgnoreThis($value, string $fieldName): void
    {
        if ($this->find(
            model: $value->model,
            value: $value->value,
            fieldName: $fieldName,
            ignoreThisParam: $value->ignoreThisParam
        ) > 1
        ) {
            $this->addError(error: "{$fieldName} unique is required");
        }
    }

    public function validateUniqueNot($value, string $fieldName): void
    {
        if (!$this->find(model: $value->model, value: $value->value, fieldName: $fieldName)) {
            $this->addError(error: "{$fieldName} is not exists");
        }
    }

    public function validateDate($date, string $fieldName): void
    {
        if (null != $date && false === strtotime($date)) {
            $this->addError(error: "{$fieldName} is not a valid date");
        }
    }

    public function validateInt($value, string $fieldName): void
    {
        if (false !== !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError(error: "{$fieldName} is not ineger type");
        }
    }

    public function validateString($value, string $fieldName): void
    {
        if (!is_string($value)) {
            $this->addError(error: "{$fieldName} is not string type");
        }
    }

    public function validateUniqueArray($value, string $fieldName): void
    {
        if ($value != array_unique($value)) {
            $this->addError(error: "{$fieldName} is repeat values");
        }
    }

    public function validateForeignKey($value, string $fieldName): void
    {
        $fielNameMessage = $fieldName;

        isset($value->foreign_key) ? $fieldName = $value->foreign_key : '';

        foreach ($value->value as $atual_value) {
            if (0 === $this->find(model: $value->model, value: $atual_value, fieldName: $fieldName)) {
                $this->addError(error: "{$fielNameMessage} = $atual_value is not exists");
            }
        }
    }

    private function find(string $model, $value, string $fieldName, array $ignoreThisParam = []): int
    {
        $namespace = "App\Models\\";

        $name_class = $namespace.ucfirst("{$model}Model");

        $object_table = new $name_class();

        $object_query_builder = $this->queryBuilder->table(table: $object_table::TABLE);

        $result_query_builder = call_user_func([$object_query_builder, 'find'], [$fieldName => $value])->getResult();

        $count = count($result_query_builder);

        foreach ($ignoreThisParam as $key => $value) {
            foreach ($result_query_builder as $result_query_builder_value) {
                if (isset($result_query_builder_value[$key]) && $result_query_builder_value[$key] != $value) {
                    return 2;
                }
            }
        }

        if (isset($result_query_builder[$fieldName])) {
            return 1;
        }

        return $count;
    }
}
