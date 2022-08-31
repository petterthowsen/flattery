<?php

namespace ThowsenMedia\Flattery\Pages;

use ThowsenMedia\Flattery\View\View;

class PhpPageRenderer implements PageRendererInterface {

    private Page $page;

    private View $view;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render(): string
    {
        $view = View::make($this->page->getFile());
        $view->overrideSource($this->page->getSource());
        return $view->render();
    }

}