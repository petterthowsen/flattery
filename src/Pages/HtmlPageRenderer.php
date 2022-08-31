<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\HTML\Element;

class HtmlPageRenderer implements PageRendererInterface {

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

        event()->trigger('hook.flattery.textPageRenderer.render', $this->page, $element);
        
        return $element;
    }

}