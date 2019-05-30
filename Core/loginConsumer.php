<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classLoader.php';

$consumer = new \Core\Tools\LoginConsumer();
$consumer->consume();


