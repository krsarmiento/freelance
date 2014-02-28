<?php

ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/src/controllers.php';

$app->run();
