<?php

namespace ThowsenMedia\Flattery\Theme;

use Symfony\Component\Yaml\Yaml;
use ThowsenMedia\Flattery\HTML\Element;
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

    public function setBlockContent(string $blockName, string $content, bool $save = true)
    {
        $data = data();
        $data->set('themes.' .$this->name, "blocks.$blockName", $content, $save);
    }
    
    public function isBlockEmpty(string $name)
    {
        if (data()->has('themes.' .$this->name, 'blocks.' .$name)) {
            $value = data()->get('themes.' .$this->name, 'blocks.' .$name);
            $text = strip_tags($value);
            return strlen($text) == 0;
        }else {
            return true;
        }
    }

    public function renderBlock(string $name)
    {
        $data = data();

        $themeName = $this->name;

        $content = '';
        
        if ($data->has("themes.$themeName", "blocks.$name")) {
            $content = $data->get("themes.$themeName", "blocks.$name");
        }
        
        $element = Element::div([
            'id' => 'flattery-block-' .slugify($name),
            'class' => 'flattery-block',
        ])->innerHtml($content);
        
        if ($this->isBlockEmpty($name)) {
            $element->addClass('flattery-block-empty');
        }

        event()->trigger('hook.flattery.theme.renderBlock', $this, $name, $content, $element);
        
        return $element;
    }

    public function getConfig(string $key)
    {
        return array_get($key, $this->config);
    }

    public function getStyles(): array
    {
        $styles = [];

        foreach($this->getConfig('styles') as $style) {
            $styles[] = '/assets/themes/' .$this->name .'/' .$style;
        }

        return $styles;
    }

}