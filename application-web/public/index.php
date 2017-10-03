<?php

require __DIR__ . '/../../vendor/autoload.php';

define('APP_ROOT_PATH', realpath(__DIR__ . '/..'));

$bootstrap = new \Web\Bootstrap();
$bootstrap->run();