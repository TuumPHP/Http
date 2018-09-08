<?php
namespace App\Demo\Chain;

use App\App\Controller\JumpController;
use App\App\Controller\LoginPresenter;
use App\Demo\Handler\CsRfToken;
use App\Demo\Handler\Dispatcher;
use App\Demo\Handler\NotFound;
use App\Demo\Middleware;
use App\Demo\Routes;
use Http\Factory\Diactoros\ResponseFactory;
use Http\Factory\Diactoros\StreamFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
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
            App::class        => [$this, 'getApp'],
            Middleware::class => [$this, 'getMiddleware'],
            Routes::class     => [$this, 'getRoutes'],
            Responder::class  => [$this, 'getResponder'],
            Dispatcher::class => [$this, 'getDispatcher'],
            NotFound::class   => [$this, 'getNotFound'],
            CsRfToken::class  => [$this, 'getCsRfToken'],
            LoginPresenter::class  => [$this, 'getLoginPresenter'],
            ResponseFactoryInterface::class => [$this, 'getResponseFactory'],
            StreamFactoryInterface::class => [$this, 'getStreamFactory'],
            JumpController::class, [$this, 'getJumpController'],
        ];
    }
    
    public function getResponseFactory()
    {
        return new ResponseFactory();
    }

    public function getStreamFactory()
    {
        return new StreamFactory();
    }

    public function getResponder(ContainerInterface $container)
    {
        $settings = $container->get('settings');

        return Factory::new($settings)
            ->setContainer($container)
            ->build();
    }

    public function getApp(ContainerInterface $container)
    {
        return new App($container, $container->get(Middleware::class));
    }

    public function getMiddleware()
    {
        return new Middleware();
    }

    public function getRoutes(ContainerInterface $container)
    {
        return new Routes($container->get(Responder::class));
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
    
    public function getLoginPresenter(ContainerInterface $container)
    {
        return new LoginPresenter($container->get(Responder::class));
    }
    
    public function getJumpController(ContainerInterface $container)
    {
        return new JumpController($container->get(Responder::class));
    }
}