<?php

namespace ThowsenMedia\Flattery\Theme;

use ThowsenMedia\Flattery\View\View;

class Theme {

    protected string $name;
    protected string $directory;

    public function __construct(string $name, string $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
    }
    
    public function getView($named = null, array $variables = []): View
    {
        if ($named == null) $named = $this->name;
        
        $file = $this->directory .'/' .$named .'.php';
        return new View($file, $variables);
    }

}