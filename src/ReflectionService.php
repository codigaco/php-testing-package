<?php

namespace Codigaco\Testing;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

class ReflectionService
{
    public function getValue($instance, string $route, $default = null)
    {
        $value = is_array($instance) ? (object)$instance : $instance;
        foreach (explode('.', $route) as $propertyName) {
            try {
                $property = $this->getProperty(get_class($value), $propertyName);
            } catch (ReflectionException $exception) {
                return $default;
            }
            $isPublic = $property->isPublic();
            $property->setAccessible(true);
            $value = $property->getValue($value);
            $property->setAccessible($isPublic);
        }
        return $value;
    }

    /**
     * @throws ReflectionException
     */
    public function getProperty(string $class, string $name): ReflectionProperty
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->hasProperty($name)) {
            return $reflection->getProperty($name);
        }

        $parentClass = $reflection->getParentClass();
        if (false !== $parentClass) {
            return $this->getProperty($parentClass->getName(), $name);
        }

        throw new ReflectionException(sprintf('property %s not found %s', $name, $class));
    }

    /**
     * @throws ReflectionException
     */
    public function hasProperty(string $class, string $name): bool
    {
        $reflection = new ReflectionClass($class);
        if ($reflection->hasProperty($name)) {
            return true;
        }

        $parentClass = $reflection->getParentClass();
        if (false !== $parentClass) {
            return $this->hasProperty($parentClass->getName(), $name);
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    public function getProperties(string $className, bool $isParentClass = false): array
    {
        $reflection = new ReflectionClass($className);

        $parentClass = $reflection->getParentClass();
        if (false === $parentClass) {
            return $reflection->getProperties();
        }

        return array_merge($reflection->getProperties(), $this->getProperties($parentClass->getName(), true));
    }

    /**
     * @throws ReflectionException
     */
    public function newInstanceWithProperties(string $class, array $attributes): object
    {
        $reflection = new ReflectionClass($class);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($this->getProperties($class) as $property) {
            $this->setProperty($instance, $property->getName(), $attributes[$property->getName()] ?? null);
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    public function newInstanceWithConstructor(string $class, array $attributes): object
    {
        $reflection = new ReflectionClass($class);

        if (null === $reflection->getConstructor()) {
            return new $class();
        }

        $arguments = array_map(static function (ReflectionParameter $parameter) use ($attributes) {
            return $attributes[$parameter->getName()] ?? null;
        }, $reflection->getConstructor()->getParameters());

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @throws ReflectionException
     */
    public function setProperty(object $instance, string $key, $value): void
    {
        $property = $this->getProperty(get_class($instance), $key);
        $isPublic = $property->isPublic();
        $property->setAccessible(true);
        $property->setValue($instance, $value);
        $property->setAccessible($isPublic);
    }

    /**
     * @throws ReflectionException
     */
    public function type(string $class, string $propertyName): string
    {
        $property = $this->getProperty($class, $propertyName);

        if ($property->hasType()) {
            return $property->getType()?->getName();
        }

        $annotations = $property->getDocComment();

        if ($annotations && preg_match('/@var ([^\n\s]+)/', $annotations, $matches)) {
            preg_match_all('/(\w+)/', $matches[1], $typesFound);
            [, $types] = $typesFound;

            return $types[0];
        }

        throw new RuntimeException(sprintf("Undefined type of %s in %s", $propertyName, $class));
    }
}
