<?php
namespace App\App;

use Psr\Container\ContainerInterface;
use \InvalidArgumentException;

class Container implements ContainerInterface
{
    /**
     * @var callable[]|mixed[]
     */
    private $factories = [];

    /**
     * @var mixed[]
     */
    private $entries = [];
    
    /**
     * Container constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->factories = $config;
    }

    /**
     * @param Provider $provider
     */
    public function loadServices($provider)
    {
        $services = $provider->getServices();
        foreach($services as $key => $factory) {
            $this->set($key, $factory);
        }
    }

    /**
     * @param string $id
     * @param mixed $target
     */
    public function set($id, $target)
    {
        $this->factories[$id] = $target;
    }
    
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new InvalidArgumentException("not found: ". $id);
        }
        if (array_key_exists($id, $this->entries)) {
            return $this->entries[$id];
        }
        if (array_key_exists($id, $this->factories)) {
            $found = $this->factories[$id];
            if (is_callable($found)) {
                $found = $found($this);
            }
            $this->entries[$id] = $found;
            return $found;
        } elseif (class_exists($id) && method_exists($id, 'forge')) {
            $found = $id::forge($this);
            $this->entries[$id] = $found;
            return $found;
        }
        throw new InvalidArgumentException("id:" . $id);
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
        if (array_key_exists($id, $this->entries)) {
            return true;
        }
        if (array_key_exists($id, $this->factories)) {
            return true;
        }
        if (class_exists($id) && method_exists($id, 'forge')) {
            return true;
        }
        return false;
    }
}