<?php

require_once '../bootstrap.php';

const FLATTERY_CONSOLE = false;

use ThowsenMedia\Flattery\CMS;

$app = CMS::getInstance();
$response = $app->run();

$response->send();