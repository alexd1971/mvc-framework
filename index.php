<?php
require_once 'core/Loader.php';

$config = include 'app/config/main.php';

spl_autoload_register('\core\Loader::autoLoad');

$application = new \app\Application();
$application->run();
