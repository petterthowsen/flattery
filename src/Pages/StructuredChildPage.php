<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\HTML\Element;

class StructuredChildPage extends Page
{

    protected array $siblings = [];

    public function __construct(string $name, string $file, array $data, string $source)
    {
        parent::__construct($name, $file, $data, $source);
        $this->fetchSiblings();
    }

    protected function fetchSiblings()
    {
        $f = explode('/', $this->file);
        array_pop($f);

        $pathSegments = array_slice($f, 0, count($f) - 1);
        $parentDir = implode('/', $pathSegments) .'/children';
        $parentName = $pathSegments[count($pathSegments) - 1];

        foreach(scandir($parentDir) as $siblingFile) {
            if ($siblingFile != '.' && $siblingFile != '..') {
                list($name, $extension) = explode('.', $siblingFile, 2);
                $this->siblings[] = $name;
            }
        }
    }

    public function getRoutePath():string
    {
        $name = explode('/', $this->name);
        foreach($name as $k => $n) {
            if ($n == 'children') {
                array_unset($k, $name);
            }
        }

        return implode('/', $name);
    }

    public function getParentRoutePath():string
    {
        $name = explode('/', $this->name);
        return implode('/', array_slice($name, 0, count($name) - 2));
    }

    public function renderMenu(array $classes = [], string $activeClass = 'active')
    {
        $str = '';
        
        foreach($this->siblings as $name)
        {
            $link = url('/' .$this->getParentRoutePath() .'/' .$name);
            
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