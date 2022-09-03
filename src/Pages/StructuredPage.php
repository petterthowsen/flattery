<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\HTML\Element;

class StructuredPage extends Page {

    /**
     * The child pages
     * These are lazy-loaded
     */
    private array $children = [];

    private string $directory;

    public function __construct(string $name, string $file, array $data, string $source)
    {
        parent::__construct($name, $file, $data, $source);
        $this->directory = preg_replace("/\/+/", '/', rtrim(dirname($file), '/'));
        $this->fetchChildren();
    }

    public function fetchChildren()
    {
        foreach(scandir($this->directory .'/children') as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            $name = trim(str_replace($this->directory, '', $file), '/');
            list($name, $extension) = explode('.', $name, 2);
            
            $this->children[$name] = $this->directory .'/'.$name .'.' .$extension;
        }
    }
    
    public function getChildren():array
    {
        return $this->children;
    }
    
    public function renderMenu(array $classes = [], string $activeClass = 'active')
    {
        $str = '';
        
        foreach($this->children as $name => $path)
        {
            $link = url($this->name .'/' .$name);
            
            $a = new Element(
                'a',
                false,
                [
                    'class' => $classes,
                    'href' => $link,
                ],
            );
            
            $a->innerHtml(ucwords(preg_replace("/[-_]/", ' ', $name)));
            
            $str .= $a;
        }
        
        return $str;
    }
    
}