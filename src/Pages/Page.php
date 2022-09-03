<?php

namespace ThowsenMedia\Flattery\Pages;

/**
 * Represents a page in the filesystem.
 */
class Page {

    /**
     * Name of the file - this is the file name without the .extension
     */
    protected string $name;

    /**
     * Full path to the page file
     */
    protected string $file;

    /**
     * File extension
     */
    protected string $extension;

    /**
     * The source code (after the --- at the top, if it exists.)
     */
    protected string $_source;

    protected array $_data;

    protected PageRendererInterface $renderer;

    public function __construct(string $name, string $file, array $data, string $source)
    {
        $this->name = $name;
        $this->file = $file;
        $this->_data = $data;
        $this->_source = $source;

        $exploded = explode('.', $file);
        $this->extension = array_pop($exploded);
    }

    public function getRoutePath():string
    {
        return $this->name;
    }

    public function setRendererClass(string $rendererClass)
    {
        $this->rendererClass = $rendererClass;
    }

    public function getRenderer(): PageRendererInterface
    {
        if ( ! isset($this->renderer)) {
            $this->renderer = new $this->rendererClass($this);
        }

        return $this->renderer;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getFile(): string
    {
        return $this->file;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getData(string $key)
    {
        return array_get($key, $this->_data);
    }

    public function getSource(): string
    {
        return $this->_source;
    }

    public function __get(string $key)
    {
        return $this->getData($key);
    }

    public function render(): string
    {
        return $this->getRenderer()->render();
    }
    
    public function __toString(): string
    {
        return $this->render();
    }
    
}