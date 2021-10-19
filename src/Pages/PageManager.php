<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\Data\Data;

class PageManager {

    private string $pagesDirectory;

    private array $extensions = [];

    public function __construct(string $pagesDirectory, array $extensions = [])
    {
        $this->pagesDirectory = $pagesDirectory;
        $this->extensions = $extensions;
    }

    public function getFile(string $name)
    {
        $fileName = $this->pagesDirectory .'/' .$name;
        
        foreach($this->extensions as $ext) {
            $file = $fileName .'.' .$ext;
            if (file_exists($file)) {
                return $file;
            }
        }
    }

    public function exists(string $name)
    {
        $fileName = $this->pagesDirectory .'/' .$name;


        foreach($this->extensions as $ext) {
            $file = $fileName .'.' .$ext;
            if (file_exists($file)) {
                return true;
            }
        }
        
        return false;
    }

}