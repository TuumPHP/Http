<?php
namespace Tuum\Respond\Builder;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var ContainerInterface[]
     */
    private $containers = [];

    /**
     * @var callable[]
     */
    private $providers = [];

    /**
     * @var mixed[]
     */
    private $concretes = [];

    /**
     * Container constructor.
     */
    public function __construct()
    {
    }
    
    public function addContainers(ContainerInterface $container): void
    {
        $this->containers[] = array_merge([$container], $this->containers);
    }

    public function addProvider(ServiceProviderInterface $provider): void
    {
        foreach($provider->getFactories() as $id => $provider) {
            $this->providers[$id] = $provider;
        }
    }
    
    public function set(string $id, $concrete): self
    {
        $this->concretes[$id] = $concrete;
        return $this;
    }

    public function setFactory(string $id, callable $factory): self
    {
        $this->providers[$id] = $factory;
        return $this;
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
        if (array_key_exists($id, $this->concretes)) {
            return $this->concretes[$id];
        }
        if (array_key_exists($id, $this->providers) && is_callable($this->providers[$id])) {
            $factory = $this->providers[$id];
            return $this->concretes[$id] = $factory($this);
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
        if (array_key_exists($id, $this->concretes)) {
            return true;
        }
        if (array_key_exists($id, $this->providers)) {
            return true;
        }
        return false;
    }
}