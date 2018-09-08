<?php
namespace App\App;

use App\Demo\Controller\ForbiddenController;
use App\Demo\Controller\JumpController;
use App\Demo\Controller\LoginPresenter;
use App\Demo\Controller\UploadController;
use App\Demo\Controller\UploadViewer;
use Psr\Container\ContainerInterface;
use Tuum\Locator\FileMap;
use Tuum\Respond\Builder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\Plates;
use Zend\Diactoros\Response;

class Provider
{
    /**
     * @var array
     */
    private $options = [];
    
    /**
     * Provider constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        $list = [];
        $list[Responder::class] = [$this, 'getResponder'];
        $list[JumpController::class] = [$this, 'getJumpController'];
        $list[UploadController::class] = [$this, 'getUploadController'];
        $list[UploadViewer::class] = [$this, 'getUploadViewer'];
        $list[ForbiddenController::class] = [$this, 'getForbiddenController'];
        $list[DocumentMap::class] = [$this, 'getDocumentMap'];
        $list[LoginPresenter::class] = [$this, 'getLoginPresenter'];

        return $list;
    }
    
    /**
     * @param Container $container
     * @return JumpController
     */
    public function getJumpController(Container $container)
    {
        return new JumpController($container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return UploadController
     */
    public function getUploadController(Container $container)
    {
        return new UploadController($container->get(UploadViewer::class), $container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return UploadViewer
     */
    public function getUploadViewer(Container $container)
    {
        return new UploadViewer($container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return ForbiddenController
     */
    public function getForbiddenController(Container $container)
    {
        return new ForbiddenController($container->get(Responder::class));
    }

    /**
     * @param ContainerInterface $container
     * @return Responder
     */    
    public function getResponder(ContainerInterface $container)
    {
        $builder = Builder::forge('TuumDemo')
            ->setContainer($container)
            ->setRenderer(Plates::forge($container->get('template-path')))
            ->setErrorOption($container->get('error-files'));
        $responder = Responder::forge($builder)
            ->setResponse(new Response());
        Respond::setResponder($responder);
        
        return $responder;
    }

    /**
     * @param ContainerInterface $container
     * @return DocumentMap
     */
    public function getDocumentMap(ContainerInterface $container)
    {
        $docs_dir = dirname(dirname(__DIR__)) . '/docs';
        $mapper   = FileMap::forge($docs_dir);
        
        return new DocumentMap($mapper, $container->get(Responder::class));
    }

    /**
     * @param ContainerInterface $container
     * @return LoginPresenter
     */
    public function getLoginPresenter(ContainerInterface $container)
    {
        return new LoginPresenter($container->get(Responder::class));
    }
}