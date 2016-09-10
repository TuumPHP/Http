<?php
namespace App\App;

use App\App\Controller\JumpController;
use App\App\Controller\PaginationController;
use App\App\Controller\UploadController;
use App\App\Controller\UploadViewer;
use Tuum\Pagination\Pager;
use Tuum\Respond\Helper\TuumProvider;
use Tuum\Respond\Responder;

class Provider
{
    /**
     * @var TuumProvider
     */
    private $provider;

    /**
     * Provider constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->provider = new TuumProvider($options);
    }

    /**
     * @param Container $container
     */
    public function load(Container $container)
    {
        $self = Provider::class;
        $list = $this->provider->getServices();
        $list[JumpController::class] = [$self, 'getJumpController'];
        $list[UploadController::class] = [$self, 'getUploadController'];
        $list[UploadViewer::class] = [$self, 'getUploadViewer'];
        $list[PaginationController::class] = [$self, 'getPaginationController'];

        foreach($list as $key => $factory) {
            $container->set($key, $factory);
        }
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
}