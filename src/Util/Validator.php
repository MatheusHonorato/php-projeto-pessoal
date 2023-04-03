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

    public function validateRequired(mixed $value, string $fieldName): void
    {
        if (empty($value)) {
            $this->addError(error: "{$fieldName} is required");
        }
    }

    public function validateEmail(mixed $value, string $fieldName): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(error: "{$fieldName} is not a valid email address");
        }
    }

    public function validateUnique(mixed $value, string $fieldName): void
    {
        if ($this->find(model: $value->model, value: $value->value, fieldName: $fieldName) > 0) {
            $this->addError(error: "{$fieldName} unique is required");
        }
    }

    public function validateUniqueIgnoreThis(mixed $value, string $fieldName): void
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

    public function validateUniqueNot(mixed $value, string $fieldName): void
    {
        if (!$this->find(model: $value->model, value: $value->value, fieldName: $fieldName)) {
            $this->addError(error: "{$fieldName} is not exists");
        }
    }

    public function validateDate(mixed $date, string $fieldName): void
    {
        if (null != $date && false === strtotime($date)) {
            $this->addError(error: "{$fieldName} is not a valid date");
        }
    }

    public function validateInt(mixed $value, string $fieldName): void
    {
        if (false !== !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError(error: "{$fieldName} is not ineger type");
        }
    }

    public function validateString(mixed $value, string $fieldName): void
    {
        if (!is_string($value)) {
            $this->addError(error: "{$fieldName} is not string type");
        }
    }

    public function validateUniqueArray(mixed $value, string $fieldName): void
    {
        if ($value != array_unique($value)) {
            $this->addError(error: "{$fieldName} is repeat values");
        }
    }

    public function validateForeignKey(mixed $value, string $fieldName): void
    {
        $fielNameMessage = $fieldName;

        isset($value->foreign_key) ? $fieldName = $value->foreign_key : '';

        foreach ($value->value as $atualValue) {
            if (0 === $this->find(model: $value->model, value: $atualValue, fieldName: $fieldName)) {
                $this->addError(error: "{$fielNameMessage} = $atualValue is not exists");
            }
        }
    }

    private function find(string $model, mixed $value, string $fieldName, array $ignoreThisParam = []): int
    {
        $namespace = "App\Models\\";

        $nameClass = $namespace.ucfirst(string: "{$model}Model");

        $objectTable = new $nameClass();

        $objectQueryBuilder = $this->queryBuilder->table(table: $objectTable::TABLE);

        $resultQueryBuilder = call_user_func([$objectQueryBuilder, 'find'], [$fieldName => $value])->getResult();

        foreach ($ignoreThisParam as $key => $value) {
            foreach ($resultQueryBuilder as $resultQueryBuilderValue) {
                if (isset($resultQueryBuilderValue[$key]) && $resultQueryBuilderValue[$key] != $value) {
                    return 2;
                }
            }
        }

        if (isset($resultQueryBuilder[$fieldName])) {
            return 1;
        }

        return count($resultQueryBuilder);
    }
}
