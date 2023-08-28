#!/usr/bin/env php
<?php

use App\Command;

require __DIR__.'/../vendor/autoload.php';

(new Command('ydict.php'))->run();
