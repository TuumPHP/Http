<?php

use App\App\Dispatcher;
use Tuum\Respond\Responder;

return function () {

    $app = new Dispatcher();
    
    return $app;
};