<?php
namespace Tuum\Http\Service;

/**
 * Interface SessionStorageInterface
 * 
 * defines API for storing data to session. 
 * this API is taken from Aura.Session's segment. 
 *
 * @package Tuum\Application\Service
 */
interface SessionStorageInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function get($key, $alt = null);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setFlash($key, $value);

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlash($key, $alt = null);

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlashNext($key, $alt=null);
}