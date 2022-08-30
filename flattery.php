<?php

require_once 'bootstrap.php';
require_once 'src/functions.php';

const FLATTERY_CONSOLE = true;

use ThowsenMedia\Flattery\CMS;

$app = CMS::getInstance();
$app->runConsole();

