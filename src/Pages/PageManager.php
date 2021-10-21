<?php

namespace ThowsenMedia\Flattery\Pages;

use Symfony\Component\Yaml\Yaml;
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

    public function isPageStructured(string $name): bool
    {
        $fileName = $this->pagesDirectory .'/' .$name;
        return is_dir($fileName);
    }

    public function textToHTML(string $text): string
    {
        return str_replace("\n", "<br>", $text);
    }

    /**
     * @return array [
     *  'settings' => [
     *     'title' => '...'
     *  ],
     *  'content' => '<p>Hello, world</p>''
     * ]
     */
    public function loadFile(string $fileName)
    {
        $src = file_get_contents($fileName);
        $src = explode("\n===", $src, 2);
        $settings = [
            
        ];

        if (count($src) == 2) {
            $settings = Yaml::parse($src[0]);
            $content = $src[1];
        }else {
            $content = $src[0];
        }

        $settings['content'] = $content;
        
        return $settings;
    }

}