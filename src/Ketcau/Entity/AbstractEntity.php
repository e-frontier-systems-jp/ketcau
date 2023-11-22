<?php

namespace Ketcau\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Doctrine\ORM\Mapping\Id;
use Ketcau\DependencyInjection\Facade\AnnotationReaderFacade;
use Ketcau\Util\StringUtil;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractEntity implements \ArrayAccess
{
    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $method = $inflector->classify($offset);

        return method_exists($this, $method)
            || method_exists($this, "get$method")
            || method_exists($this, "is$method")
            || method_exists($this, "has$method");
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $method = $inflector->classify($offset);

        if(method_exists($this, $method)) {
            return $this->$method;
        } elseif (method_exists($this, "get$method")) {
            return $this->{"get$method"}();
        } elseif (method_exists($this, "is$method")) {
            return $this->{"is$method"}();
        } elseif (method_exists($this, "has$method")) {
            return $this->{"has$method"}();
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
    }


    public function setPropertiesFromArray(array $arrProps, array $excludeAttribute = [], \ReflectionClass $parentClass = null)
    {
        $objReflect = null;
        if (is_object($parentClass)) {
            $objReflect = $parentClass;
        } else {
            $objReflect = new \ReflectionClass($this);
        }

        $arrProperties = $objReflect->getProperties();
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            if (in_array($name, $excludeAttribute) || !array_key_exists($name, $arrProps)) {
                continue;
            }
            $objProperty->setValue($this, $arrProps[$name]);
        }

        $parentClass = $objReflect->getParentClass();
        if (is_object($parentClass)) {
            self::setPropertiesFromArray($arrProps, $excludeAttribute, $parentClass);
        }
    }


    public function toArray(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'], \ReflectionClass $parentClass = null)
    {
        $objReflect = null;
        if ($parentClass) {
            $objReflect = $parentClass;
        } else {
            $objReflect = new \ReflectionClass($this);
        }

        $arrProperties = $objReflect->getProperties();
        $arrResults = [];
        foreach ($arrProperties as $objProperty) {
            $objProperty->setAccessible(true);
            $name = $objProperty->getName();
            if (in_array($name, $excludeAttribute)) {
                continue;
            }
            $arrResults[$name] = $objProperty->getValue($this);
        }

        $parentClass = $objReflect->getParentClass();
        if (is_object($parentClass)) {
            $arrParents = self::toArray($excludeAttribute, $parentClass);
            if (!is_array($arrParents)) {
                $arrParents = [];
            }
            if (!is_array($arrResults)) {
                $arrParents = [];
            }
            $arrResults = array_merge($arrParents, $arrResults);
        }

        return $arrResults;
    }


    public function toNormalizedArray(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        $arrResult = $this->toArray($excludeAttribute);
        foreach ($arrResult as &$value) {
            if ($value instanceof \DateTime) {
                $value->setTimezone(new \DateTimeZone('UTC'));
                $value = $value->format('Y-m-d\TH:i:s\Z');
            } elseif ($value instanceof AbstractEntity) {
                $value = $this->getEntityIdentifierAsArray($value);
            } elseif ($value instanceof Collection) {
                $Collections = $value;
                $value = [];
                foreach ($Collections as $Child) {
                    $value[] = $this->getEntityIdentifierAsArray($Child);
                }
            }
        }

        return $arrResult;
    }

    public function toJSON(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        return json_encode($this->toNormalizedArray($excludeAttribute));
    }


    public function toXML(array $excludeAttribute = ['__initializer__', '__cloner__', '__isInitialized__'])
    {
        $ReflectionClass = new \ReflectionClass($this);
        $serializer = new Serializer([new PropertyNormalizer()], [new XmlEncoder([XmlEncoder::ROOT_NODE_NAME => $ReflectionClass->getShortName()])]);

        $xml = $serializer->serialize($this->toNormalizedArray($excludeAttribute), 'xml');
        if ('\\' === DIRECTORY_SEPARATOR) {
            $xml = StringUtil::convertLineFeed($xml, "\r\n");
        }

        return $xml;
    }


    public function copyProperties($srcObject, array $excludeAttribute = [])
    {
        $this->setPropertiesFromArray($srcObject->toArray($excludeAttribute), $excludeAttribute);

        return $this;
    }

    public function getEntityIdentifierAsArray(AbstractEntity $Entity)
    {
        $Result = [];
        $PropReflect = new \ReflectionClass($Entity);
        if ($Entity instanceof Proxy) {
            $PropReflect = $PropReflect->getParentClass();
        }
        $properties = $PropReflect->getProperties();

        foreach ($properties as $Property) {
            $AnnotationReader = AnnotationReaderFacade::create();
            $anno = $AnnotationReader->getPropertyAnnotation($Property, Id::class);
            if ($anno) {
                $Property->setAccessible(true);
                $Result[$Property->getName()] = $Property->getValue($Entity);
            }
        }

        return $Result;
    }
}