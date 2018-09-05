<?php
namespace Tuum\Respond;

use Tuum\Respond\Builder\Container;

class Factory
{
    private $setting = [];
    public function forge(): Responder
    {
        $provider  = new Builder\Builder($this->setting);
        $container = new Container();
        $container->addProvider($provider);
        $responder = new Responder($container);
        
        return $responder;
    }
}