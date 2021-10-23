<?php

namespace ThowsenMedia\Flattery\Pages;

/**
 * Represents a page in the filesystem.
 */
class Page {
    
    /**
     * Name of the file - this is the file name without the .extension
     */
    private string $name;

    /**
     * Full path to the page file
     */
    private string $file;

    /**
     * File extension
     */
    private string $extension;

    private array $_data;

    public function __construct(string $name, string $file, array $data)
    {
        $this->name = $name;
        $this->file = $file;
        $this->_data = $data;

        $exploded = explode('.', $file);
        $this->extension = array_pop($exploded);
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