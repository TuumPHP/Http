<?php
namespace App\App;

use App\App\Controller\JumpController;
use App\App\Controller\PaginationController;
use App\App\Controller\UploadController;
use App\App\Controller\UploadViewer;
use Tuum\Pagination\Pager;
use Tuum\Respond\Helper\ProviderTrait;
use Tuum\Respond\Responder;

class Provider
{
    use ProviderTrait;

    /**
     * Provider constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @param Container $container
     */
    public function load(Container $container)
    {
        $list = $this->getRespondList();
        $list[JumpController::class] = 'getJumpController';
        $list[UploadController::class] = 'getUploadController';
        $list[UploadViewer::class] = 'getUploadViewer';
        $list[PaginationController::class] = 'getPaginationController';

        foreach($list as $key => $method) {
            $container->set($key, [$this, $method]);
        }
    }

    /**
     * @param Container $container
     * @return PaginationController
     */
    public function getPaginationController(Container $container)
    {
        return new PaginationController($container->get(Responder::class), new Pager());
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
}