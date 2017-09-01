<?php
namespace Tuum\Respond\Interfaces;

/**
 * Interface ErrorFileInterface
 * 
 * finds template file name for the http $status code error. 
 *
 * @package Tuum\Respond\Interfaces
 */
interface ErrorFileInterface
{
    /**
     * returns template file name for $status. 
     *
     * @param int   $status
     * @return string
     */
    public function find($status);
}