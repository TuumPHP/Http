<?php

use App\App\Dispatcher;
use Tuum\Respond\Responder;

return function () {

    $app = new Dispatcher();
    $app->getContainer()->load(new \App\App\Provider([
        'renderer' => 'plates',
        'template-path' => __DIR__ . '/plates',
    ]));
    
    return $app;
};