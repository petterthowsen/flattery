<?php

require_once 'bootstrap.php';
require_once 'src/functions.php';

use ThowsenMedia\Flattery\CMS;

$app = CMS::getInstance();
$app->run();