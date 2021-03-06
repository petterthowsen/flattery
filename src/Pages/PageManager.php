<?php

namespace ThowsenMedia\Flattery\Pages;

use Symfony\Component\Yaml\Yaml;
use ThowsenMedia\Flattery\Data\Data;

class PageManager {

    private string $pagesDirectory;

    private array $extensions = [];

    private array $renderers = [];
    
    /**
     * @param Page[] loaded pages
     */
    private array $pages = [];

    public function __construct(string $pagesDirectory, array $extensions = [])
    {
        $this->pagesDirectory = $pagesDirectory;
        $this->extensions = $extensions;
    }

    public function mapRenderer(string $extension, string $rendererClassName)
    {
        $this->renderers[$extension] = $rendererClassName;
    }

    /**
     * Get the class name of the renderer that handles the given extension
     */
    public function getRendererFor(string $extension): string
    {
        return $this->renderers[$extension];
    }

    private function tryExtensions(string $fileName)
    {
        foreach($this->extensions as $ext) {
            $file = $fileName .'.' .$ext;
            if (file_exists($file)) {
                return $file;
            }
        }

        return null;
    }

    public function getFile(string $name)
    {
        if ($this->isPageStructured($name)) {
            return $this->tryExtensions($this->pagesDirectory .'/' .$name .'/' .$name);
        }else {
            return $this->tryExtensions($this->pagesDirectory .'/' .$name);
        }
    }

    public function exists(string $name)
    {
        if ($this->isPageStructured($name)) {
            return true;
        }
        
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

    protected function load(string $name): Page
    {

        $structured = $this->isPageStructured($name);

        $file = $this->getFile($name);

        $src = file_get_contents($file);
        $src = explode("\n---", $src, 2);

        $info = [];
        if (count($src) == 2) {
            $info = Yaml::parse($src[0]);
            $content = $src[1];
        }else {
            $content = $src[0];
        }

        if ($structured) {
            $page = new StructuredPage($name, $file, $info, $content);
        }else {
            $page = new Page($name, $file, $info, $content);
        }
        
        $renderer = $this->getRendererFor($page->getExtension());
        $page->setRendererClass($renderer);

        $this->pages[$name] = $page;

        return $page;
    }

    public function get(string $name): Page
    {
        if ( ! isset($this->pages[$name])) {
            $this->load($name);
        }

        return $this->pages[$name];
    }

}