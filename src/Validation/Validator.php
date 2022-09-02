<?php

namespace ThowsenMedia\Flattery\Validation;

class Validator
{

    protected static $ruleTypes = [];

    public static function registerRuleType(string $name, callable $callable)
    {
        static::$ruleTypes[$name] = $callable;
    }

    public static function getRuleTypes():array
    {
        return static::$ruleTypes;
    }

    public static function runRule(string $rule, array $ruleOptions = [], mixed $value)
    {
        $callable = static::$ruleTypes[$rule];
        $args = $ruleOptions;
        array_unshift($args, $value);
        return call_user_func_array($callable, $args);
    }

    public static function parseRulesArray(array $raw_rules): array
    {
        $rules = [];
        foreach($raw_rules as $field => $rulesList)
        {
            $rules[$field] = static::parseRulesList($rulesList);
        }
        return $rules;
    }

    public static function parseRulesList(string $rulesList):array
    {
        $rules = [];
        foreach(explode('|', $rulesList) as $rule)
        {
            list($rule, $options) = explode(':', $rule, 2);
            $options = preg_split("/, ?/", $options);
            if ($options == false) {
                $options = [];
            }
            $rules[] = ['rule' => $rule, 'options' => $options];
        }
        return $rules;
    }

    protected array $rawRules;

    protected array $rules;

    public function __construct(array $rules = [])
    {
        $this->rawRules = $rules;
        $this->rules = static::parseRulesArray($this->rawRules);
    }

    public function getRules():array
    {
        return $this->rules;
    }

}