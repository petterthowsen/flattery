<?php

namespace ThowsenMedia\Flattery;

use ThowsenMedia\Flattery\Console\Commands\InstallPluginCommand;
use ThowsenMedia\Flattery\Console\Console;
use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\HTTP\Kernel;
use ThowsenMedia\Flattery\HTTP\Request;
use ThowsenMedia\Flattery\HTTP\Session;
use ThowsenMedia\Flattery\Pages\PageManager;
use ThowsenMedia\Flattery\Theme\Theme;
use ThowsenMedia\Flattery\View\View;
use ThowsenMedia\Flattery\Container;
use ThowsenMedia\Flattery\Extending\PluginLoader;
use ThowsenMedia\Flattery\HTTP\Response;
use ThowsenMedia\Flattery\Pages\Page;

/**
 * @property Event $event
 * @property Request $request
 * @property Kernel $kernel
 * @property Data $data
 * @property Session $session
 * @property PluginLoader $plugins
 * @property PageManager $pages
 */
class CMS extends Container {

    private static $instance;

    public static function getInstance()
    {
        if ( ! isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    private function __construct()
    {
        $this->bind('event', Event::class, true);
        $this->bind('request', request::class, true);
        $this->bind('kernel', Kernel::class, true);
        
        $this->bindClosure('data', Data::class, function() {
            return new Data(FLATTERY_PATH_DATA);
        }, true);
        
        $this->bindClosure('session', Session::class, function() {
            return Session::getInstance();
        }, true);

        $this->bindClosure('plugins', PluginLoader::class, function() {
            return new PluginLoader(FLATTERY_PATH_PLUGINS);
        }, true);

        $this->bindClosure('theme', Theme::class, [$this, 'loadTheme'], true);
        
        $this->bindClosure('pages', PageManager::class, function() {
            $extensions = data()->get('config.system', 'pageManager.extensions');
            return new PageManager(FLATTERY_PATH_PAGES, $extensions);
        }, true);

        $this->bindClosure('console', Console::class, function() {
            $app = new Console();
            $app->add(new InstallPluginCommand());
            return $app;
        }, true);
    }

    public Page $currentPage;

    private $styles = [];
    private $scripts = [];

    /**
     * Load the active theme.
     */
    public function loadTheme(): Theme
    {
        $name = $this->data->get('config.system', 'theme');
        $theme = new Theme($name, FLATTERY_PATH_THEMES .'/' .$name);
        
        $this->addStyles($theme->getStyles());

        return $theme;
    }

    public function addStyles(array $styles)
    {
        foreach($styles as $style) {
            if ( ! in_array($style, $this->styles)) {
                $this->styles[] = $style;
            }
        }
    }

    public function addStyle($style)
    {
        if ( ! in_array($style, $this->styles)) {
            $this->styles[] = $style;
        }
    }

    public function addScripts(array $scripts)
    {
        foreach($scripts as $script) {
            $this->addScript($script);
        }
    }

    public function addScript($script)
    {
        if ( ! in_array($script, $this->scripts)) {
            $this->scripts[] = $script;
        }
    }

    public function getStylesForView(): string
    {
        $styles = '';
        foreach($this->styles as $style) {
            $styles .= "<link rel='stylesheet' href='$style'/>";
        }

        return $styles;
    }
    
    public function getScriptsForView(): string
    {
        $scripts = '';
        foreach($this->scripts as $script) {
            $scripts .= "<script src='$script'></script>";
        }

        return $scripts;
    }

    public function run(): Response
    {
        # handle asset requests
        $this->kernel->attachHandler('assetRequestHandler', [$this, 'assetRequestHandler'], -10);

        # handle web requests
        $this->kernel->attachHandler('pageRequestHandler', [$this, 'pageRequestHandler']);
        
        # editorjs
        $this->addScript('https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest');
        
        # go !
        $response = $this->kernel->handle($this->request);
        
        # return the response and handle errors
        if ( ! $response) {
            return $this->handle404();
        }

        return $response;
    }
    
    /**
     * 
     */
    public function runConsole()
    {
        $this->console->run();
    }

    public function assetRequestHandler(Request $request, callable $next)
    {
        if ($request->segment(0) == 'assets') {
            $segments = $request->getSegments();
            array_shift($segments);
            
            $type = array_shift($segments);
            $file = implode('/', $segments);
            
            if ($type == 'themes') {
                $file = FLATTERY_PATH_THEMES .'/' .$file;
                
                if ( ! file_exists($file)) {
                    return;
                }
                
                $response = new Response();

                $temp = explode('.', $file);
                $extension = end($temp);
                
                switch ($extension) {
                    case "jpg":
                        $response->setHeader('Content-type', 'image/jpeg');
                        break;
                    case "jpeg":
                        $response->setHeader('Content-type', 'image/jpeg');
                        break;
                    case "gif":
                        $response->setHeader('Content-type', 'image/gif');
                        break;
                    case "png":
                        $response->setHeader('Content-type', 'image/png');
                        break;
                    case "css":
                        $response->setHeader('Content-type', 'text/css');
                        break;
                    default:
                        die;
                }
                
                $response->setContent(file_get_contents($file));

                return $response;
            }
        }else {
            return $next();
        }
    }
    
    public function pageRequestHandler(Request $request, callable $next)
    {
        # what page is the homepage?
        $homepage = $this->data->get('config.system', 'homepage');
        
        # get the page we are requesting
        # or just use the homepage
        $pageName = $request->segment(0) ?? $homepage;

        # check if the page exists
        if ($this->pages->exists($pageName)) {
            $this->currentPage = $this->pages->get($pageName);

            $view = $this->getViewForPage($this->currentPage);
            
            $response = new Response();
            $response->setContent($view->render());
            
            return $response;
        }else {
            return $next();
        }
    }

    public function getViewForPage(Page $page): View
    {
        # get the view
        $view = $this->theme->getView();

        $this->event->trigger('hook.flattery.getViewForPage', $view);

        # return the view with the page
        return $view->with([
            'page' => $page,
            'siteName' => $this->data->get('config.system', 'siteName'),
            'styles' => $this->getStylesForView(),
            'scripts' => $this->getScriptsForView(),
        ]);
    }

    protected function handle404()
    {
        http_response_code(404);

        $pageName = $this->data->get('config.system', 'errorPages.404');
        if ($pageName) {
            $view = $this->getViewForPage($pageName)->with([
                'pageName' => $pageName,
            ]);
        }else {
            return Response::make("Oops! I can't find that page...!");
        }
    }

    public function renderMenu(string $name = 'primaryNavigation')
    {
        $this->event->trigger('hook.flattery.renderMenu_before');
        $items = $this->data->get('config.navigation', 'menus.' .$name);
        
        $str = '';
        
        foreach($items as $label => $link) {
            //$link = $this->event->trigger('hook.flattery.renderMenu_link', $link);
            $str .= "<li><a href='$link'>$label</a></li>";
        }

        return $str;
    }

}