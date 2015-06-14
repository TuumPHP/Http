<?php

call_user_func(function() {

    $vendor = dirname(__DIR__).'/vendor';
    $auto   = '/autoload.php';
    if (file_exists($vendor)) {
        /** @noinspection PhpIncludeInspection */
        include_once($vendor.$auto);
    }
    $vendor = dirname(dirname(dirname(__DIR__)));
    if (file_exists($vendor)) {
        /** @noinspection PhpIncludeInspection */
        include_once($vendor.$auto);
    }
},null);

