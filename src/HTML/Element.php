<?php

namespace ThowsenMedia\Flattery\HTML;

class Element {

    private string $tagName = 'div';
    private bool $selfClosing = false;

    private $_attributes;

    private $_children = [];

    public ?string $innerHtml = null;

    public function __construct(string $tagName, bool $selfClosing = false, array $attributes = [])
    {
        $this->tagName = $tagName;
        $this->selfClosing = $selfClosing;
        $this->_attributes = $attributes;

        # make sure classes is an array
        if ( isset($this->_attributes['class'])) {
            $class = & $this->_attributes['class'];

            if (is_string($class)) {
                $class = explode(' ', $class);
            }
        }
    }

    public static function div($attributes = []): self
    {
        return new static('div', false, $attributes);
    }

    public function innerHtml(?string $value): self
    {
        $this->innerHtml = $value;
        return $this;
    }

    public function setAttribute(string $name, $value): self
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    public function getAttribute(string $name): string
    {
        return $this->_attributes[$name] ?? null;
    }

    public function removeAttribute(string $name): self
    {
        unset($this->_attributes[$name]);
        return $this;
    }

    public function addClass($class)
    {
        if ( ! is_array($class)) {
            $class = explode(' ', $class);
        }

        if ( ! isset($this->_attributes['class'])) {
            $this->_attributes['class'] = [];
        }

        foreach($class as $cls)
        {
            if ( ! in_array($cls, $this->_attributes['class'])) {
                $this->_attributes['class'][] = $cls;
            }
        }

        return $this;
    }

    public function append($child)
    {
        $this->_children[] = $child;
    }

    public function prepend($child)
    {
        array_unshift($this->_children, $child);
    }

    public function renderAttributes(): string
    {
        $str = '';
        foreach($this->_attributes as $name => $value) {
            if ($name == 'class') {
                $value = implode(' ', $value);
            }
            $str .= $name .'="' .$value .'" ';
        }

        return rtrim($str);
    }

    public function render()
    {
        $str = '<' .$this->tagName;
        if (count($this->_attributes) > 0) {
            $str .= ' ' .$this->renderAttributes();
        }

        $str .= ">\n";

        if ($this->selfClosing == false) {

            if ($this->innerHtml) $str .= $this->innerHtml;

            foreach($this->_children as $child) {
                $str .= (string) $child ."\n";
            }

            $str .= "</" .$this->tagName .">";
        }

        return $str;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function __set($name, $value)
    {
        $this->_children[$name] = $value;
    }

    public function __get($name)
    {
        return $this->_children[$name];
    }

    public function __isset($name): bool
    {
        return isset($this->_children[$name]);
    }

}