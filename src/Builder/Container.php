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
     * Container constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->addContainers($container);
    }
    
    public static function forge($setup = []): self
    {
        $provider = new Builder($setup);
        $container = new self($provider);
        
        return $container;
    }
    
    /**
     * @param ContainerInterface $container
     */
    public function addContainers(ContainerInterface $container): void
    {
        $this->containers[] = array_merge([$container], $this->containers);
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
        return false;
    }
}