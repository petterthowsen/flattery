<?php

namespace ThowsenMedia\Flattery\Pages;

interface PageRendererInterface {

    public function __construct(string $source);
    
    public function render();

}