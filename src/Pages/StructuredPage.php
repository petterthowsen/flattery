<?php

namespace ThowsenMedia\Flattery\Pages;

class StructuredPage extends Page {

    /**
     * The child pages
     * These are lazy-loaded
     */
    private array $_children = [];

    private string $directory;

    public function __construct(string $name, string $file, array $data, string $source)
    {
        parent::__construct($name, $file, $data, $source);
        $this->directory = dirname($file);
    }

    public function getPages(): array
    {
        
    }

}