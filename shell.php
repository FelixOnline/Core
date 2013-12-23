<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/SafeSQL.php';
$config = require __DIR__ . '/config.php';

$app = new \FelixOnline\Core\App($config);

$boris = new \Boris\Boris('felix> ');
$boris->setLocal(array('app' => $app));
$boris->start();
