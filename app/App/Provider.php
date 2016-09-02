<?php
namespace App\App;

use Tuum\Respond\Helper\ProviderTrait;

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
        foreach($list as $key => $method) {
            $container->set($key, [$this, $method]);
        }
    }
}