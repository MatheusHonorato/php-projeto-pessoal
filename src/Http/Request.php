<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorInterface;
use stdClass;

class Request implements RequestInterface
{
    public static function validate(
        ?ValidatorInterface $validator = null,
        ?array $params = [],
        ?array $extraDatas = []
    ): \stdClass {
        $request = self::getParams($extraDatas);

        if (!$validator) {
            return $request;
        }

        $validator = self::executeValidatorRules(params: $params, extraDatas: $extraDatas, request: $request, validator: $validator);

        if ($validator->getErrors()) {
            return (object) $validator->getErrors();
        }

        return $request;
    }

    public static function getHttpMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getCurrentUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    private static function getParams(array $extraDatas): stdClass
    {
        $GET = array_map(
            fn ($value) => htmlspecialchars(string: $value),
            (array) $_GET
        );

        $request = (object) array_merge(
            (array) json_decode(
                json: file_get_contents(filename: 'php://input', use_include_path : true)
            ),
            (array) $extraDatas,
            (array) $GET
        );

        return $request;
    }

    private static function executeValidatorRules(array $params, array $extraDatas, object $request, ValidatorInterface $validator): ValidatorInterface
    {
        foreach ($params as $key => $rules) {
            foreach ($rules as $rule) {
                $valueValidatorRule = self::getValidatorRule(rule: $rule);

                $validatorRuleTerms = self::getValidatorRuleTerms(valueValidatorRule: $valueValidatorRule, rule: $rule);

                [$nameAction, $valueParam] = self::generateNameAction(validatorRuleTerms: $validatorRuleTerms, request: $request, key: $key, valueValidatorRule: $valueValidatorRule, extraDatas: $extraDatas);

                $validator->$nameAction($valueParam, $key);
            }
        }

        return $validator;
    }

    private static function getValidatorRule(mixed $rule): array|null
    {
        return strpos(haystack: $rule, needle: ':') ? explode(separator: ':', string: $rule) : null;
    }

    private static function getValidatorRuleTerms(?array $valueValidatorRule, mixed $rule): array|null
    {
        $ruleTerms = is_array(value: $valueValidatorRule) ? (array) explode(separator: '-', string: $valueValidatorRule[0]) : (array) $rule;

        return array_map(
            fn ($term) => ucfirst($term),
            $ruleTerms
        );
    }

    private static function getIgnoreTheseParams(string $nameMethod, array $extraDatas): array
    {
        if ('UniqueIgnoreThis' == $nameMethod) {
            return ['id' => $extraDatas['id']];
        }

        return [];
    }

    private static function getModelNameAndForeignKey(?array $valueValidatorRule): array
    {
        isset($valueValidatorRule[1]) ? $model = $valueValidatorRule[1] : $model = '';
        isset($valueValidatorRule[2]) ? $foreignKey = $valueValidatorRule[2] : $foreignKey = '';

        return [$model, $foreignKey];
    }

    private static function getNameMethod(?array $terms): string
    {
        return implode(separator: '', array: $terms);
    }

    private static function generateNameAction(?array $validatorRuleTerms, object $request, string|int $key, ?array $valueValidatorRule, array $extraDatas): array
    {
        $nameMethod = self::getNameMethod(terms: $validatorRuleTerms);

        [$model, $foreignKey] = self::getModelNameAndForeignKey(valueValidatorRule: $valueValidatorRule);

        $ignoreThisParam = self::getIgnoreTheseParams(nameMethod: $nameMethod, extraDatas: $extraDatas);

        $value_param = self::getValuesParams(requestKey: $request?->$key, model: $model, foreignKey: $foreignKey, valueValidatorRule: $valueValidatorRule, ignoreThisParam: $ignoreThisParam);

        $nameAction = "validate$nameMethod";

        return [$nameAction, $value_param];
    }

    private static function getValuesParams(mixed $requestKey, string $model, mixed $foreignKey, mixed $valueValidatorRule, array $ignoreThisParam): mixed
    {
        if (null == $valueValidatorRule) {
            return $requestKey;
        }

        return (object) ['model' => $model, 'value' => $requestKey, 'foreign_key' => $foreignKey, 'ignoreThisParam' => $ignoreThisParam];
    }
}
