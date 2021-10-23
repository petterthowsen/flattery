<?php

namespace ThowsenMedia\Flattery\Theme;

use Symfony\Component\Yaml\Yaml;
use ThowsenMedia\Flattery\View\View;

class Theme {

    protected string $name;
    protected string $directory;

    protected array $config;

    public function __construct(string $name, string $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
        $configFile = $this->directory .'/' .$name .'.yml';
        $this->config = Yaml::parseFile($configFile);
    }

    public function getView($named = null, array $variables = []): View
    {
        if ($named == null) $named = $this->name;
        
        $file = $this->directory .'/' .$named .'.php';
        return new View($file, $variables);
    }

    public function getConfig(string $key)
    {
        return array_get($key, $this->config);
    }

    public function getStyles(): array
    {
        $styles = [];

        foreach($this->getConfig('styles') as $style) {
            $styles[] = './assets/themes/' .$this->name .'/' .$style;
        }

        return $styles;
    }

}