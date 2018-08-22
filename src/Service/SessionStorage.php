<?php
namespace Tuum\Respond\Service;

use Aura\Session\Segment;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder\Payload;

/**
 * Class SessionStorage
 *
 * a default implementation for SessionStorageInterface
 * using Aura.Session 2.0 or above.
 *
 * @package Tuum\Respond\Service
 */
class SessionStorage implements SessionStorageInterface
{
    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Payload
     */
    private $viewData;
    
    /**
     * @param Session $session
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * construct a SessionStorage using Aura.Session as a default implementation.
     *
     * @param string     $name
     * @param null|array $cookie
     * @return SessionStorage
     */
    public static function forge($name, $cookie = null)
    {
        $factory = new SessionFactory();
        $cookie  = $cookie ?: $_COOKIE;
        $session = $factory->newInstance($cookie);
        $self    = new self($session);
        $self->start();

        return $self->withStorage($name);
    }

    /**
     * @return Payload
     */    
    public function getViewData()
    {
        if (isset($this->viewData)) {
            return $this->viewData;
        }
        if ($viewData = $this->getFlash(PayloadInterface::MY_KEY)) {
            $this->viewData = clone($viewData);
            return $this->viewData;
        }
        $this->viewData = new Payload();
        
        return $this->viewData;
    }

    /**
     * 
     */
    public function saveViewData()
    {
        if (!isset($this->viewData)) {
            return;
        }
        $this->setFlash(PayloadInterface::MY_KEY, $this->viewData);
    }

    /**
     * starts a session
     *
     * @return $this
     */
    public function start()
    {
        if (!isset($_SESSION)) {
            $this->session->start();
        }

        return $this;
    }

    /**
     * create a new segment
     *
     * @param string $name
     * @return SessionStorage
     */
    public function withStorage($name)
    {
        $self          = clone($this);
        $self->segment = $this->session->getSegment($name);

        return $self;
    }

    /**
     * @return null
     */
    public function commit()
    {
        return $this->session->commit();
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->segment->set($key, $value);
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function get($key, $alt = null)
    {
        return $this->segment->get($key, $alt);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setFlash($key, $value)
    {
        $this->segment->setFlash($key, $value);
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlash($key, $alt = null)
    {
        return $this->segment->getFlash($key, $alt);
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlashNext($key, $alt = null)
    {
        return $this->segment->getFlashNext($key, $alt);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validateToken($value)
    {
        $token = $this->session->getCsrfToken();

        return $token->isValid($value);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->session->getCsrfToken()->getValue();
    }
}