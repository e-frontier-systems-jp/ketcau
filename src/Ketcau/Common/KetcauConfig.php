<?php

namespace Ketcau\Common;

use Symfony\Component\DependencyInjection\ContainerInterface;

class KetcauConfig implements \ArrayAccess
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function get($key)
    {
        return $this->container->getParameter($key);
    }


    public function has($key)
    {
        return $this->container->hasParameter($key);
    }


    public function set($key, $value)
    {
        $this->container->setParameter($key, $value);
    }


    public function __get($key)
    {
        return $this->get($key);
    }



    /**
     * @param mixed $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }


    /**
     * @param mixed $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }


    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }


    /**
     * @param mixed $offset
     * @return void
     * @throws \Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception();
    }
}