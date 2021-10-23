<?php

namespace ThowsenMedia\Flattery\Pages;

/**
 * Represents a page in the filesystem.
 */
class Page {
    
    private string $name;

    private string $file;

    private array $_data;

    public function __construct(string $name, string $file, array $data)
    {
        $this->_data = $data;   
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getFile(): string
    {
        return $this->file;
    }

    public function getData(string $key)
    {
        return array_get($key, $this->_data);
    }

    public function __get(string $key)
    {
        return $this->getData($key);
    }

}