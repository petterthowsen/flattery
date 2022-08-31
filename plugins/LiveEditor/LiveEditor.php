<?php

use ThowsenMedia\Flattery\Extending\Plugin;
use ThowsenMedia\Flattery\HTML\Element;
use ThowsenMedia\Flattery\HTTP\Response;
use ThowsenMedia\Flattery\Pages\Page;
use ThowsenMedia\Flattery\Theme\Theme;
use ThowsenMedia\Flattery\View\View;

class LiveEditor extends Plugin {

    
    public function register()
    {
        router()->post('api/liveeditor/blocks/save', [$this, 'postApiBlockSave']);
    }
    
    public function run()
    {
        # don't show LiveEdit on the control panel
        if (request()->starts_with('controlpanel')) {
            return;
        }

        # add our scripts and styles when we render a page view
        event()->listen('hook.flattery.getViewForPage', function(View $view) {
            if (auth()->isAdmin()) {
                flattery()->addScript('//cdn.quilljs.com/1.3.6/quill.js');
                flattery()->addScript(asset('plugins/LiveEditor/js/liveeditor.js'));
                
                flattery()->addStyle(asset('plugins/LiveEditor/css/liveeditor.css'));
                flattery()->addStyle('//cdn.quilljs.com/1.3.6/quill.snow.css');
            }
        });
        
        # when we render a block,
        # instead of just rendering the content, we'll setup a special element using editorjs
        event()->listen('hook.flattery.theme.renderBlock', function(Theme $theme, string $name, $content, Element $element) {
            # remove the innerHtml
            $element->innerHtml = null;

            $element->content = new Element('div', false, [
                'class' => 'flattery-block--content'
            ]);

            $element->content->innerHtml = $content;
            
            $element->content->setAttribute('title', 'Click to edit ' .$name);
            $element->setAttribute('data-block-name', $name);
        });

        event()->listen('hook.flattery.textPageRenderer.render', function(Page $page, Element $element) {
            
            if ($page->getExtension() == 'txt') {
                $content = $element->innerHtml;
                $element->innerHtml = null;
                
                $element->content = new Element('div');
                $element->content->addClass('flattery-page--content');
                $element->content->innerHtml = $content;
            }
        });
    }


    public function postApiBlockSave()
    {
        if ( ! auth()->isAdmin()) {
            return Response::make("Page not found", 404);
        }

        if ( ! isset($_POST['block'])) {
            $response = Response::make("Missing block parameter", 400);
            return $response;
        }

        $block = $_POST['block'];
        $content = $_POST['content'] ?? '';

        flattery()->theme->setBlockContent($block, $content);

        return Response::make('Block Saved', 200);
    }

}