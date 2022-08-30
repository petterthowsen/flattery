<?php

require_once '../bootstrap.php';
require_once '../src/functions.php';

const FLATTERY_CONSOLE = false;

use ThowsenMedia\Flattery\CMS;

$app = CMS::getInstance();
$response = $app->run();

$response->send();