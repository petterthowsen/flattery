<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\HTML\Element;

class MarkdownPageRenderer implements PageRendererInterface {

    private Page $page;

    private string $source;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render(): string
    {
        $element = new Element("div");
        $element->innerHtml = $this->page->getSource();

        $element->setAttribute('id', 'flattery-page--' .slugify($this->page->getName()));
        $element->addClass('flattery--page');
        $element->addClass('flattery--page--markdown');

        event()->trigger('hook.flattery.textPageRenderer.render', $this->page, $element);
        
        return $element;
    }

}