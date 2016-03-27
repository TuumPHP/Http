<?php
use App\Config\Define\GuardConfig;
use App\Config\Define\LoggerFactory;
use App\Config\Define\ResponderFactory;
use App\Config\Handlers\ErrorHandler;
use App\Config\Handlers\NotFoundHandler;
use Psr\Log\LoggerInterface;
use Slim\DefaultServicesProvider;
use App\Config\Utils\Container;
use Tuum\Builder\AppBuilder;
use Tuum\Respond\Responder;

/**
 * settings for Slim's Container.
 *
 * @param AppBuilder $builder
 * @return Container
 */
return function (AppBuilder $builder) {

    /** @var Container $container */
    $container = Container::forge();

    $container['notFoundHandler']      = function ($c) {return new NotFoundHandler($c);};
    $container['errorHandler']         = function ($c) {return new ErrorHandler($c);};
    $container['csrf']                 = function ($c) {return GuardConfig::forge($c);};
    $container[Responder::class]       = function ($c) {return ResponderFactory::forge($c);};
    $container[LoggerInterface::class] = new LoggerFactory($builder);

    $setting = [
            'httpVersion'                       => '1.1',
            'responseChunkSize'                 => 4096,
            'outputBuffering'                   => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails'               => false,
        ] + $builder->get('options');
    if ($builder->debug) {
        $setting['displayErrorDetails'] = true;
    }

    $container['settings'] = $setting;

    $defaults = new DefaultServicesProvider();
    /** @noinspection PhpParamsInspection */
    $defaults->register($container);

    return $container;
};
