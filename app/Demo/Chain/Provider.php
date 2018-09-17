<?php
namespace App\Demo\Chain;

use App\Demo\App;
use App\Demo\Controller\DocumentMap;
use App\Demo\Controller\ForbiddenController;
use App\Demo\Controller\JumpController;
use App\Demo\Controller\LoginPresenter;
use App\Demo\Controller\UploadController;
use App\Demo\Controller\UploadViewer;
use App\Demo\Handler\CatchThrows;
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
use Tuum\Locator\FileMap;
use Tuum\Respond\Builder\ServiceProviderInterface;
use Tuum\Respond\Factory;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

class Provider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories()
    {
        return [
            App::class                      => [$this, 'getApp'],
            Middleware::class               => [$this, 'getMiddleware'],
            Routes::class                   => [$this, 'getRoutes'],
            // middleware
            NotFound::class                 => [$this, 'getNotFound'],
            CsRfToken::class                => [$this, 'getCsRfToken'],
            Dispatcher::class               => [$this, 'getDispatcher'],
            CatchThrows::class              => [$this, 'getCatchThrows'],
            // services
            Responder::class                => [$this, 'getResponder'],
            ResponseFactoryInterface::class => [$this, 'getResponseFactory'],
            StreamFactoryInterface::class   => [$this, 'getStreamFactory'],
            // controllers and presenters
            LoginPresenter::class           => [$this, 'getLoginPresenter'],
            JumpController::class           => [$this, 'getJumpController'],
            UploadController::class         => [$this, 'getUploadController'],
            UploadViewer::class             => [$this, 'getUploadViewer'],
            ForbiddenController::class      => [$this, 'getForbiddenController'],
            DocumentMap::class              => [$this, 'getDocumentMap'],
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
        $responder = Factory::new($settings)
            ->setContainer($container)
            ->build();
        Respond::setResponder($responder); // a quick access to responder!?
        
        return $responder;
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

    public function getUploadController(ContainerInterface $container)
    {
        return new UploadController(
            $container->get(UploadViewer::class), 
            $container->get(Responder::class)
        );
    }

    public function getUploadViewer(ContainerInterface $container)
    {
        return new UploadViewer($container->get(Responder::class));
    }
    
    public function getForbiddenController(ContainerInterface $container)
    {
        return new ForbiddenController($container->get(Responder::class));
    }

    public function getCatchThrows(ContainerInterface $container)
    {
        return new CatchThrows($container->get(Responder::class));
    }

    public function getDocumentMap(ContainerInterface $container)
    {
        $docs_dir = dirname(dirname(dirname(__DIR__))) . '/docs';
        $mapper   = FileMap::forge($docs_dir);

        return new DocumentMap($mapper, $container->get(Responder::class));
    }
}