#!/usr/bin/env php
<?php

use App\Command;

if (! isset($GLOBALS['_composer_autoload_path'])) {
    $GLOBALS['_composer_autoload_path'] = __DIR__.'/../vendor/autoload.php';
}

require $GLOBALS['_composer_autoload_path'];

(new Command('ydict.php'))->run();
