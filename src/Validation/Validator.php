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

    public static function getRuleError(string $field, $value, string $rule, array $options)
    {
        $field = humanize($field);
        
        $key = "errors.$rule";
        if (data()->has('config.validation', $key)) {
            $error = data()->get('config.validation', $key);
            $error = str_replace('$field', $field, $error);
            foreach($options as $key => $value)
            {
                $error = str_replace('$' .$key, $value, $error);
            }
            return $error;
        }else {
            return "The $field is invalid.";
        }
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
                foreach($rules as $rule) {
                    $error = static::runRule($rule['rule'], $rule['options'], $value);
                    if ($error !== true) {
                        if ( ! isset($this->errors[$field])) {
                            $this->errors[$field] = [];
                        }

                        $this->errors[$field][] = static::getRuleError($field, $value, $rule['rule'], $rule['options']);
                    }
                }
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

    public function getErrors(bool $groupedByFields = true):array
    {
        if ($groupedByFields == false) {
            $flattened = [];
            foreach($this->errors as $field => $errors) {
                $flattened = array_merge($flattened, $errors);
            }

            return $flattened;
        }
        return $this->errors;
    }

}