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
            $ruleData = explode(':', $rule, 2);
            if (count($ruleData) > 1) {
                list($rule, $options) = explode(':', $rule, 2);
                
                $options = preg_split("/, ?/", $options);
                if ($options == false) {
                    $options = [];
                }
            }else {
                $options = [];
            }       
            
            $rules[] = ['rule' => $rule, 'options' => $options];
        }
        return $rules;
    }

    protected array $rawRules;

    protected array $rules;

    protected array $errors = [];
    
    public function __construct(array $rules = null)
    {
        if (isset($rules)) {
            $this->rawRules = $rules;
            $this->rules = static::parseRulesArray($this->rawRules);
        }
    }

    public function setRules(array $rules)
    {
        $this->rawRules = $rules;
        $this->rules = static::parseRulesArray($this->rawRules);
    }

    public function getRules():array
    {
        return $this->rules;
    }
    
    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function passes(array $values): bool
    {
        foreach($values as $field => $value)
        {
            if (isset($this->rules[$field])) {
                $rules = $this->rules[$field];
                $error = static::runRule($rule['rule'], $rule['options'], $value);
            }
        }

        return count($this->errors) == 0;
    }
    
    protected function addError(string $field, string $message)
    {
        if ( ! isset($this->errors[$field]))
            $this->errors[$field] = [];
        
        $this->errors[$field][] = $message;
    }

    public function getErrors():array
    {
        return $this->errors;
    }

}