<?php

use App\Demo\App;
use Tuum\Respond\Builder\Container;

/**
 * @param array $settings
 * @return App
 */
function bootDemo(array $settings)
{
    $settings  = $settings + [
            'debug' => false,
        ];
    $container = new Container();
    $container->set('settings', $settings);
    $container->addProvider(new \App\Demo\Chain\Provider());

    return $container->get(App::class);
}
