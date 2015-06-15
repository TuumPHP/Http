<?php

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;
use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

class ContainerException extends \RuntimeException  implements ContainerExceptionInterface{}
class NotFoundException extends ContainerException implements NotFoundExceptionInterface {}

class Container implements ContainerInterface
{
    private $data = [];

    /**
     * set something...
     *
     * @param string $id
     * @param mixed  $target
     */
    public function set($id, $target)
    {
        $this->data[$id] = $target;
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
            throw new NotFoundException();
        }
        return $this->data[$id];
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
        return array_key_exists($id, $this->data);
    }
}