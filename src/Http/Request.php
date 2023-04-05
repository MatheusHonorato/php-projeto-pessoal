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

        foreach ($params as $key => $rules) {
            foreach ($rules as $rule) {
                $valueRule = strpos(haystack: $rule, needle: ':') ? explode(separator: ':', string: $rule) : null;

                $ruleTerms = is_array(value: $valueRule) ? (array) explode(separator: '-', string: $valueRule[0]) : (array) $rule;

                $ruleTerms = array_map(
                    fn ($term) => ucfirst($term),
                    $ruleTerms
                );

                $lastNameMethod = implode(separator: '', array: $ruleTerms);

                $ignoreThisParam = [];

                isset($valueRule[1]) ? $model = $valueRule[1] : $model = '';
                isset($valueRule[2]) ? $foreignKey = $valueRule[2] : $foreignKey = '';

                if ('UniqueIgnoreThis' == $lastNameMethod) {
                    $ignoreThisParam = ['id' => $extraDatas['id']];
                }

                null == $valueRule ? $value_param = $request?->$key : $value_param = (object) ['model' => $model, 'value' => $request?->$key, 'foreign_key' => $foreignKey, 'ignoreThisParam' => $ignoreThisParam];

                call_user_func([$validator, 'validate'.$lastNameMethod], $value_param, $key);
            }
        }

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
}
