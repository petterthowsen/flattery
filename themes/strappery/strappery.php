<?=$theme->getView('views/head');?>

    <div class="strappery--header">
        <div class="container">
            <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 border-bottom">
            <a href="<?=url('/')?>" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
                <?=$siteName?>
            </a>

            <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <?=flattery()->renderMenu('primaryNavigation', 'nav-link px-2');?>
            </ul>

            <div class="col-md-3 text-end">
                <?php
                    if ( ! $theme->isBlockEmpty('header_right')) {
                        echo $theme->renderBlock('header_right');
                    }
                ?>
            </div>
            </header>
        </div>
    </div>

    <main class="strappery--main">
        <div class="container">
            <?php if ($page->title):?>
                <div class="strappery--page-title">
                    <h1><?=$page->title?></h1>
                </div>
            <?php endif;?>
            
            <div class="flattery--messages strappery--messages">
                <?php if(session()->has('flash.errors')):?>
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            <?php foreach(session()->remove('flash.errors') as $error): ?>
                                <li><?=$error?></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                <?php endif;?>
            </div>

            <?php if (pages()->isPageStructured($page->getName()) || pages()->isPageStructuredChild($page->getName())): ?>
                <div class="container strappery--structured-page">
                    <div class="row">
                        <div class="col-auto">
                            <ul>
                                <?=$page->renderMenu(); ?>
                            </ul>
                        </div>
                        
                        <div class="col">
                            <?=$page;?>
                        </div>
                    </div>
                </div>
            
            <?php else: ?>
                <?=$page->render()?>
            <?php endif; ?>
        </div>
    </main>

    <div class="strappery--footer">
        <footer class="container py-5">
            <?php foreach(['top', 'bottom'] as $block): ?>
                <div class="strappery--footer-<?=$block?> strappery--row">
                    <?php foreach(range(1, 4) as $i): ?>
                        <?php if ( auth()->isAdmin() || ! $theme->isBlockEmpty("footer_$block" ."_$i")): ?>
                            <?=theme()->renderBlock("footer_$block" ."_$i")?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </footer>
    </div>

<?=$theme->getView('views/foot')?>