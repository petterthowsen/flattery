<?php

namespace ThowsenMedia\Flattery\Pages;

class StructuredPage extends Page {

    /**
     * The child pages
     * These are lazy-loaded
     */
    private array $_children = [];

    private string $directory;

    public function __construct(string $name, string $file, array $data)
    {
        parent::__construct($name, $file, $data);
        $this->directory = dirname($file);
    }

    public function loadSubPages()
    {
        
    }

}