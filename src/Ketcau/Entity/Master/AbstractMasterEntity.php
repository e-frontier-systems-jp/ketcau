<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Self_;


/**
 * @ORM\MappedSuperclass()
 */
abstract class AbstractMasterEntity extends \ketcau\Entity\AbstractEntity
{

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="int", options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    protected int $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected string $name;


    /**
     * @var int
     *
     * @ORM\Column(name="sort_no", type="int", options="{"unsigned": true})
     */
    protected int $sort_no;


    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int $id
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param int $sortNo
     * @return $this
     */
    public function setSortNo(int $sortNo): self
    {
        $this->sort_no = $sortNo;
        return $this;
    }

    /**
     * @return int
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }


    public function __get($name)
    {
        return self::getConstantValue($name);
    }

    public function __set($name, $value)
    {
        throw new \InvalidArgumentException();
    }


    public static function __callStatic($name, $arguments)
    {
        return self::getConstantValue($name);
    }



    protected static function getConstantValue(string $name): mixed
    {
        if (in_array($name,['id','name','sortNo'])) {
            throw new \InvalidArgumentException();
        }

        $ref = new \ReflectionClass(static::class);
        $constants = $ref->getConstants();
        if (array_key_exists($name, $constants)) {
            return $constants[$name];
        }

        $refProperty = $ref->getProperty($name);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($ref->newInstance());
    }
}