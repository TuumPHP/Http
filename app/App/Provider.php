<?php
namespace App\App;

use App\App\Controller\JumpController;
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
        
        foreach($list as $key => $method) {
            $container->set($key, [$this, $method]);
        }
    }

    /**
     * @param Container $container
     * @return JumpController
     */
    public function getJumpController(Container $container)
    {
        return new JumpController($container->get(Responder::class));
    }
}