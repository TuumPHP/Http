<?php
namespace Tuum\Respond\Builder;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private $concrete = [];
    
    private $provider = [];

    /**
     * @var ContainerInterface[]
     */
    private $containers = [];
    
    public function __construct($setup = [])
    {
        $this->concrete = $setup;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        foreach($this->containers as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }
        if (array_key_exists($id, $this->concrete)) {
            return $this->concrete[$id];
        }
        if (array_key_exists($id, $this->provider)) {
            return $this->concrete[$id] = $this->build($id);
        }
        throw new NotFoundException(sprintf('Failed to get the id (%s) in the container.', $id));
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        foreach($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        if (array_key_exists($id, $this->concrete)) {
            return true;
        }
        if (array_key_exists($id, $this->provider)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    private function build(string $id)
    {
        $provider = $this->provider[$id];
        if (is_callable($provider)) {
            return $provider($this);
        }
        throw new ContainerException(sprintf('Failed to build the id (%s).', $id));
    }

    /**
     * @param string $id
     * @param mixed  $concrete
     * @return Container|$this
     */
    public function set(string $id, $concrete): self
    {
        $this->concrete[$id] = $concrete;
        return $this;
    }

    /**
     * @param string   $id
     * @param callable $provider
     * @return Container
     */
    public function setProvider(string $id, callable $provider): self 
    {
        $this->provider[$id] = $provider;
        return $this;
    }
}