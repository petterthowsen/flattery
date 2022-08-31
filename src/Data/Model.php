<?php

namespace ThowsenMedia\Flattery\Data;

class Model {

    private array $_data;

    public static function create(array $data = [])
    {
        return new static($data);
    }

    public function __construct(array $data)
    {
        $this->_data = $data;
    }
    
    public function __get($key)
    {
        return isset($this->_data[$key]) ?? null;
    }

    public function __set(string $key, $value)
    {
        $this->_data[$key] = $value;
    }

}