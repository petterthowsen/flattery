<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\HTML\Element;

class TextPageRenderer implements PageRendererInterface {

    private Page $page;

    private string $source;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render(): string
    {
        $html = $this->page->getSource();
        $html = strip_tags($html);
        $html = ltrim($html, "\n");
        $html = str_replace("\n", "<br>", $html);
        
        $element = new Element("div");
        $element->innerHtml = $html;

        $element->setAttribute('id', 'flattery-page--' .slugify($this->page->getName()));
        $element->addClass('flattery--page');

        event()->trigger('hook.flattery.textPageRenderer.render', $this->page, $element);
        
        return $element;
    }

}