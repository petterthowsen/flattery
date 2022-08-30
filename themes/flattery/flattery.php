<!DOCTYPE html>
<html lang="en">
<head lang="en-US">

    <meta charset="utf-8">
    <title><?=$page->title .' | ' .$siteName?></title>

    <?=$styles?>
</head>
<body>
    <header id="header">

        <a id="brand" href="./"><?=$siteName?></a>

        <nav id="navigation">
            <ul>
                <?=flattery()->renderMenu()?>
            </ul>
        </nav>
    </header>

    <main id="main">
        <h1><?=$page->title?></h1>

        <?=$page->render()?>
    </main>

    <footer id="footer" class="container py-2">
        <?php foreach(['top', 'bottom'] as $block): ?>
            <div class="footer-<?=$block?> row">
                <?php foreach(range(1, 4) as $i): ?>
                    <?php if ( auth()->isAdmin() or ! $theme->isBlockEmpty("footer_$block" ."_$i")): ?>
                        <div class="col">
                            <?=$theme->renderBlock("footer_$block" ."_$i")?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </footer>
    <?=$scripts?>
</body>
</html>