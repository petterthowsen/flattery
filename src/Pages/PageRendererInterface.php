<?php

namespace ThowsenMedia\Flattery\Pages;

interface PageRendererInterface {

    public function __construct(Page $page);
    
    public function render();

}