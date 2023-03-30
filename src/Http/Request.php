<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorInterface;

class Request implements RequestInterface
{
    public static function validate(
        ?ValidatorInterface $validator = null,
        ?array $params = [],
        ?array $extra_datas = []
    ): \stdClass {
        $GET = array_map(
            function ($value) {
                return htmlspecialchars(string: $value);
            },
            (array) $_GET
        );

        $request = (object) array_merge(
            (array) json_decode(
                file_get_contents(filename: 'php://input', use_include_path : true)
            ),
            (array) $extra_datas,
            (array) $GET
        );

        if (!$validator) {
            return $request;
        }

        foreach ($params as $key => $rules) {
            $request->$key = isset($request->$key) ? $request->$key : null;

            foreach ($rules as $rule) {
                $value_rule = strpos($rule, ':') ? explode(':', $rule) : null;

                $rule_terms = is_array($value_rule) ? (array) explode('-', $value_rule[0]) : (array) $rule;

                $rule_terms = array_map(
                    function ($term) {
                        return ucfirst($term);
                    },
                    $rule_terms
                );

                $lastNameMethod = implode('', $rule_terms);

                $ignoreThisParam = [];

                isset($value_rule[1]) ? $model = $value_rule[1] : $model = '';
                isset($value_rule[2]) ? $foreign_key = $value_rule[2] : $foreign_key = '';

                if ('UniqueIgnoreThis' == $lastNameMethod) {
                    $ignoreThisParam = ['id' => $extra_datas['id']];
                }

                null == $value_rule ? $value_param = $request->$key : $value_param = (object) ['model' => $model, 'value' => $request->$key, 'foreign_key' => $foreign_key, 'ignoreThisParam' => $ignoreThisParam];

                call_user_func([$validator, 'validate'.$lastNameMethod], $value_param, $key);
            }
        }

        if ($validator->getErrors()) {
            return (object) $validator->getErrors();
        }

        return $request;
    }
}
