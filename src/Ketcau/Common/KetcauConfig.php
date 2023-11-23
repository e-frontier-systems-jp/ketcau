<?php

namespace Ketcau\Common;

use Symfony\Component\DependencyInjection\ContainerInterface;

class KetcauConfig implements \ArrayAccess
{
    protected $container;


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


    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }


    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }


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