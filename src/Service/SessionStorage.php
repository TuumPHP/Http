<?php
namespace Tuum\Respond\Service;

use Aura\Session\Segment;
use Aura\Session\Session;
use Aura\Session\SessionFactory;

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
     * @param Session $session
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * construct a SessionStorage using Aura.Session as a default implementation.
     *
     * @param string $name
     * @param null|array $cookie
     * @return SessionStorage
     */
    public static function forge($name, $cookie=null)
    {
        $factory = new SessionFactory();
        $cookie  = $cookie ?: $_COOKIE;
        $session = $factory->newInstance($cookie);
        $self = new self($session);
        return $self->withStorage($name);
    }

    /**
     * create a new segment
     *
     * @param string $name
     * @return SessionStorage
     */
    public function withStorage($name)
    {
        $self = clone($this);
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
        return $this->segment->getflash($key, $alt);
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
}