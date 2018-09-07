<?php
namespace App\Demo\Chain;

use App\Demo\Handler\CsRfToken;
use App\Demo\Handler\Dispatcher;
use App\Demo\Handler\NotFound;
use App\Demo\Middleware;
use App\Demo\Routes;
use Psr\Container\ContainerInterface;
use Tuum\Respond\Builder\ServiceProviderInterface;
use Tuum\Respond\Factory;
use Tuum\Respond\Responder;

class Provider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories()
    {
        return [
            Responder::class  => [$this, 'getResponder'],
            Dispatcher::class => [$this, 'getDispatcher'],
            NotFound::class   => [$this, 'getNotFound'],
            CsRfToken::class  => [$this, 'getCsRfToken'],
        ];
    }

    public function getResponder(ContainerInterface $container)
    {
        return Factory::new([
            'template_dir' => dirname(dirname(__DIR__)) . '/plates',
        ])
            ->setContainer($container)
            ->build();
    }

    public function getApp(ContainerInterface $container)
    {
        return new App($container, $container->get(Middleware::class));
    }

    public function getDispatcher(ContainerInterface $container)
    {
        return new Dispatcher($container, $container->get(Routes::class));
    }

    public function getNotFound(ContainerInterface $container)
    {
        return new NotFound($container->get(Responder::class));
    }

    public function getCsRfToken(ContainerInterface $container)
    {
        return new CsRfToken($container->get(Responder::class));
    }
}