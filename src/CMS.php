<?php

namespace ThowsenMedia\Flattery;

use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\HTTP\Request;
use ThowsenMedia\Flattery\Pages\PageManager;
use ThowsenMedia\Flattery\Theme\Theme;
use ThowsenMedia\Flattery\View\View;

class CMS {

    public static $instance;

    public static function getInstance()
    {
        if ( ! isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private Event $event;
    private Data $data;
    private Request $request;
    private Theme $theme;

    private string $currentThemeName;
    private string $currentPage;
    private string $currentPageFile;
    
    public function get(string $what)
    {
        if (property_exists($this, $what)) {
            return $this->$what;
        }else {
            throw new Exception("Cannot get CMS->$what, unknown property.", 1);
            
        }
    }

    public function initializeTheme(string $name)
    {
        $this->theme = new Theme($name, FLATTERY_PATH_THEMES .'/' .$name);
        $this->currentThemeName = $name;
    }

    public function isUserLoggedIn()
    {
        return true;
    }

    public function isUserAdmin()
    {
        return true;
    }

    public function getFlatteryStylesForView(): string
    {
        
    }

    public function getFlatteryScriptsForView(): string
    {
        $str = '';

        if ($this->isUserAdmin()) {
            $str .= '<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>';
        }

        return $str;
    }

    public function run()
    {
        # initialize the various sub-systems...
        $this->event = new Event();
        $this->data = new Data(FLATTERY_PATH_DATA);
        $this->request = new Request();
        
        # setup the pageManager
        $extensions = $this->data->get('config.system', 'pageManager.extensions');
        $this->pageManager = new PageManager(FLATTERY_PATH_PAGES, $extensions);

        # load the theme
        $this->initializeTheme($this->data->get('config.system', 'theme'));
        
        # what page is the homepage?
        $homepage = $this->data->get('config.system', 'homepage');
        
        # get the page we are requesting
        # or just use the homepage
        $pageName = $this->request->segment(0) ?? $homepage;
        
        # check if the page exists
        if ($this->pageManager->exists($pageName)) {
            $this->currentPage = $pageName;
            $this->currentPageFile = $this->pageManager->getFile($pageName);
            
            return $this->getViewForPage($this->currentPage);
        }else {
            return $this->handle404();
        }
    }

    public function getViewForPage(string $pageName): View
    {
        $pageFile = $this->pageManager->getFile($pageName);

        # get the page
        $page = $this->pageManager->loadFile($pageFile);
        
        # get the view
        $view = $this->theme->getView();
        
        # return the view with the page
        return $view->with([
            'page' => $page,
            'siteName' => $this->data->get('config.system', 'siteName'),
            'flattery_styles' => '',
            'flattery_scripts' => $this->getFlatteryScriptsForView(),
        ]);
    }

    protected function handle404()
    {
        $pageName = $this->data->get('config.system', 'errorPages.404');
        if ($pageName) {
            $view = $this->getViewForPage($pageName)->with();
        }else {
            return "Oops! I can't find that page...!";
        }
    }

}