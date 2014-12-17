<?php
use core\Framework;

require_once 'core/Framework.php';

Framework::initialize();

Framework::registerApplication(new \app\Application());

Framework::application()->run();
