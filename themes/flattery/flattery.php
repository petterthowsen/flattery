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
        <?=$theme->renderBlock('footer_bottom_1')?>
    </footer>
    <?=$scripts?>
</body>
</html>