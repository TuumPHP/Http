<?php
namespace App\App;

use App\App\Controller\ForbiddenController;
use App\App\Controller\JumpController;
use App\App\Controller\LoginPresenter;
use App\App\Controller\UploadController;
use App\App\Controller\UploadViewer;
use Psr\Container\ContainerInterface;
use Tuum\Locator\FileMap;
use Tuum\Respond\Builder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\Plates;

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
        $self = Provider::class;
        $list = [];
        $list[Responder::class] = [$self, 'getResponder'];
        $list[JumpController::class] = [$self, 'getJumpController'];
        $list[UploadController::class] = [$self, 'getUploadController'];
        $list[UploadViewer::class] = [$self, 'getUploadViewer'];
        $list[ForbiddenController::class] = [$self, 'getForbiddenController'];
        $list[DocumentMap::class] = [$self, 'getDocumentMap'];
        $list[LoginPresenter::class] = [$self, 'getLoginPresenter'];

        return $list;
    }
    
    /**
     * @param Container $container
     * @return JumpController
     */
    public static function getJumpController(Container $container)
    {
        return new JumpController($container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return UploadController
     */
    public static function getUploadController(Container $container)
    {
        return new UploadController($container->get(UploadViewer::class), $container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return UploadViewer
     */
    public static function getUploadViewer(Container $container)
    {
        return new UploadViewer($container->get(Responder::class));
    }

    /**
     * @param Container $container
     * @return ForbiddenController
     */
    public static function getForbiddenController(Container $container)
    {
        return new ForbiddenController($container->get(Responder::class));
    }

    /**
     * @param ContainerInterface $container
     * @return Responder
     */    
    public static function getResponder(ContainerInterface $container)
    {
        $responder = Responder::forge(
            Builder::forge('TuumDemo')
                ->setContainer($container)
                ->setRenderer(Plates::forge($container->get('template-path')))
            ->setErrorOption($container->get('error-files'))
        );
        Respond::setResponder($responder);
        
        return $responder;
    }

    /**
     * @param ContainerInterface $container
     * @return DocumentMap
     */
    public static function getDocumentMap(ContainerInterface $container)
    {
        $docs_dir = dirname(dirname(__DIR__)) . '/docs';
        $mapper   = FileMap::forge($docs_dir);
        
        return new DocumentMap($mapper, $container->get(Responder::class));
    }

    /**
     * @param ContainerInterface $container
     * @return LoginPresenter
     */
    public static function getLoginPresenter(ContainerInterface $container)
    {
        return new LoginPresenter($container->get(Responder::class));
    }
}