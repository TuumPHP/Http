<?php
namespace Tuum\Respond\Builder;


interface ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories();
}