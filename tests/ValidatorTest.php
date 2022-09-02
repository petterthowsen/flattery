<?php

use PHPUnit\Framework\TestCase;
use ThowsenMedia\Flattery\Validation\Validator;
use ThowsenMedia\Flattery\Validation\BasicRulesProvider;

final class ValidatorTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        BasicRulesProvider::register();
    }
    
    public function testRuleTypesAdded()
    {
        $rules = Validator::getRuleTypes();
        $this->assertTrue(isset($rules['min']), 'min rule not added?');
    }

    public function testMin()
    {
        $this->assertTrue(Validator::runRule('min', [5], 5), '5 >= 5');
        $this->assertFalse(Validator::runRule('min', [5], 4), '4 >= 5 is true');
    }

    public function testMax()
    {
        $this->assertTrue(Validator::runRule('max', [5], 5), '5 <= 5');
        $this->assertFalse(Validator::runRule('max', [5], 6), '6 <= 5 is true');
    }
    
    public function testRulesParsedCorrectly()
    {
        $v = new Validator([
            'age' => 'min:18'
        ]);
        
        $rules = $v->getRules();
        
        $this->assertTrue(is_array($rules), 'rules is not array!');
        $this->assertArrayHasKey('age', $rules, 'rules does not have the age rule?');
        $this->assertTrue(is_array($rules['age']), 'rules.age is not an array!');
        $this->assertTrue(isset($rules['age'][0]), 'rules.age.0 is not an array!');
        $this->assertTrue(isset($rules['age'][0]['options']), 'rules.age.0.options is not an array!');
    }

}