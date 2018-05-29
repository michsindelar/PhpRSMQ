<?php

/**Get default autoloader*/
$autoloader = require './vendor/autoload.php';

/**Register new namespace*/
$autoloader->addPsr4('PhpRSMQ\Tests\\', __DIR__);
