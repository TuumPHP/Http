<?php
namespace App\App;

use App\App\Controller\JumpController;
use App\App\Controller\PaginationController;
use App\App\Controller\UploadController;
use App\App\Controller\UploadViewer;
use Interop\Container\ContainerInterface;
use League\Plates\Engine;
use Tuum\Pagination\Pager;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\TuumProvider;
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
        $list[PaginationController::class] = [$self, 'getPaginationController'];

        return $list;
    }

    /**
     * @param Container $container
     * @return PaginationController
     */
    public static function getPaginationController(Container $container)
    {
        return new PaginationController($container->get(Responder::class), new Pager());
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
     * @param ContainerInterface $container
     * @return Responder
     */    
    public static function getResponder(ContainerInterface $container)
    {
        return new Responder(
            (new Builder('TuumDemo'))
                ->setContainer($container)
                ->setRenderer(new Plates(new Engine(dirname(__DIR__) . '/plates')))
        );
    }
}