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
     * @return array
     */
    public function getService()
    {
        return $this->getRespondList();
    }
}