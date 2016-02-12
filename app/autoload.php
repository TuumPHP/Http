<?php

call_user_func(function() {

    $vendor = dirname(__DIR__).'/vendor';
    $auto   = '/autoload.php';
    if (file_exists($vendor)) {
        /** @noinspection PhpIncludeInspection */
        include_once($vendor.$auto);
    }

    $loader = new \Composer\Autoload\ClassLoader();

    $loader->addPsr4('App\\', __DIR__);
    $loader->register();

},null);

