<?php
namespace App\App;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use \InvalidArgumentException;

class Container implements ContainerInterface
{
    private $container = [];

    /**
     * Container constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->container = $config;
    }

    /**
     * @param string $id
     * @param mixed $target
     */
    public function set($id, $target)
    {
        $this->container[$id] = $target;
    }
    
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new InvalidArgumentException;
        }
        if (array_key_exists($id, $this->container)) {
            $found = $this->container[$id];
            if (is_callable($found)) {
                $found = $found($this);
            }
            return $found;
        } elseif (class_exists($id) && method_exists($id, 'forge')) {
            return $id::forge($this);
        }
        throw new InvalidArgumentException;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        if (array_key_exists($id, $this->container)) {
            return true;
        }
        if (class_exists($id) && method_exists($id, 'forge')) {
            return true;
        }
        return false;
    }
}