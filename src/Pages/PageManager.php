<?php

namespace ThowsenMedia\Flattery\Pages;

use Symfony\Component\Yaml\Yaml;
use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\HTTP\Routing\Router;

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

    public function registerRoutes(Router $router)
    {
        $self = $this;
        
        $router->get('/', function() use($self) {
            $homepage = data()->get('config.system', 'homepage');
            return $self->load($homepage);
        });

        $this->registerRoutesRecursively($router, FLATTERY_PATH_PAGES, "/");
    }

    private function registerRoutesRecursively(Router $router, string $dir, string $route)
    {
        $self = $this;

        $files = scandir($dir);
        
        foreach($files as $file) {
            # skip these
            if ($file == '.' || $file == '..') {
                continue;
            }else if (is_dir($dir .'/' .$file)) {
                $pageName = $file;
                $pageRoute = $route .$pageName;
                $pagePath = $route .$pageName .'/' .$pageName;
                if ($this->isPageStructured($pageRoute)) {
                    $router->get($pageRoute, function() use($self, $pageRoute) {
                        return $self->load($pageRoute);
                    });
                    
                    $children = scandir($dir .'/' .$file .'/children');
                    foreach($children as $child_file) {
                        if ($child_file == '.' || $child_file == '..') continue;
                        $pageName = explode('.', $child_file)[0];
                        dump($pageName);
                        $pagePath = $pageRoute .'/children/' .$pageName;
                        dump($pagePath);
                        $child_pageRoute = $pageRoute .'/' .$pageName;
                        $router->get($child_pageRoute, function() use($self, $pagePath) {
                            return $self->load($pagePath);
                        });
                    }
                }else {
                    $name = explode('.', $file)[0];
                    $this->registerRoutesRecursively($router, $dir .'/' .$file, $route .'/' .$name);
                }
            }else {
                $pageName = explode('.', $file)[0];
                $pageRoute = $route .'/' .$pageName;
                $router->get($pageRoute, function() use($self, $pageRoute) {
                    return $self->load($pageRoute);
                });
            }
        }
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
        $name = trim($name, '/');

        if ($this->isPageStructured($name)) {
            $name = $name .'/' .$name;
        }

        $file = $this->tryExtensions($this->pagesDirectory .'/' .$name);
        return $file;
    }

    public function exists(string $name)
    {
        if (file_exists($this->pagesDirectory .'/' .$name)) {
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
        $name = trim($name, '/');
        $fileName = $this->pagesDirectory .'/' .$name;
        if (is_dir($fileName)) {
            return $this->tryExtensions($fileName .'/' .$name) !== null;
        }
        return false;
    }
    
    public function isPageStructuredChild(string $name): bool
    {
        $parts = explode('/', trim($name, '/'));
        if (count($parts) > 1) {
            return $parts[1] == 'children';
        }
        
        return false;
    }
    
    public function textToHTML(string $text): string
    {
        return str_replace("\n", "<br>", $text);
    }

    protected function load(string $name): Page
    {
        $name = trim($name, '/');
        $structured = $this->isPageStructured($name);
        $child = $structured ? false : $this->isPageStructuredChild($name);
        


        $file = $this->getFile($name);
        
        $src = explode("\n---", file_get_contents($file), 2);
        
        $info = [];
        if (count($src) == 2) {
            $info = Yaml::parse($src[0]);
            $content = $src[1];
        }else {
            $content = $src[0];
        }
        
        if ($child) {
            $page = new StructuredChildPage($name, $file, $info, $content);
        }else if ($structured) {
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