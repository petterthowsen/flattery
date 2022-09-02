<?php

namespace ThowsenMedia\Flattery\HTTP;

class Input
{

    protected array $input = [];

    public function __construct()
    {
        $this->input = $_REQUEST;
    }

    public function has(string $key):bool
    {
        return isset($this->input[$key]);
    }

    public function hasFile(string $key):bool
    {
        return isset($_FILES[$key]);
    }

    public function get(...$keys):array
    {
        if (count($keys) == 1 && is_array($keys[0])) {
            $keys = $keys[0];
        }

        $values = [];
        foreach($keys as $key)
        {
            $values[$key] = $this->input[$key] ?? null;
        }

        return $values;
    }

}