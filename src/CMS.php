<?php

namespace ThowsenMedia\Flattery;

use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\HTTP\Request;
use ThowsenMedia\Flattery\Pages\PageManager;

class CMS {

    
    public static $instance;

    public static function getInstance()
    {
        if ( ! isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public Request $request;

    private string $currentPage;
    private string $currentPageFile;

    public function run()
    {
        # init Data & Request
        $this->data = new Data(FLATTERY_PATH_DATA);

        $this->request = new Request();

        $extensions = $this->data->get('config.system', 'pageManager.extensions');
        $this->pageManager = new PageManager(FLATTERY_PATH_PAGES, $extensions);

        $homepage = $this->data->get('config.system', 'homepage');
        
        # get the page we are requesting
        $pageName = $this->request->segment(0) ?? $homepage;

        if ($this->pageManager->exists($pageName)) {
            
        }

    }

}