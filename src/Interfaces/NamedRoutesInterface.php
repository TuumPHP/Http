<?php
namespace Tuum\Respond\Interfaces;

interface NamedRoutesInterface
{
    /**
     * returns url for a give route name. 
     * 
     * @param string $routeName
     * @param array  $options
     * @return string
     */
    public function route($routeName, $options = []);
}