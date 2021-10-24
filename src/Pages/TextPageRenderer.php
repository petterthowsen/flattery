<?php

namespace ThowsenMedia\Flattery\Pages;

class TextPageRenderer implements PageRendererInterface {

    private string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function render(bool $plain = false): string
    {
        if ($plain) return $this->source;

        $html = $this->source;

        $html = str_replace("\n", "<br>", $html);
        
        return $html;
    }

}