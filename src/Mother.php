<?php

declare(strict_types=1);

namespace Codigaco\Testing;

use BadMethodCallException;
use Codigaco\Testing\Faker\Factory;
use Codigaco\Testing\Faker\Generator;
use ReflectionClass;
use ReflectionException;

abstract class Mother implements MotherInterface
{
    protected Generator $faker;
    protected ReflectionService $reflectionService;
    private array $data;

    public function __construct(?string $locale = null)
    {
        $this->faker = Factory::create($locale ?? Factory::DEFAULT_LOCALE);
        $this->reflectionService = new ReflectionService();
        $this->reset();
    }

    public function __call(string $name, array $arguments): mixed
    {
        if (preg_match('/^with([A-Z][a-zA-Z0-9]*)$/', $name, $matches)) {
            return $this->with(lcfirst($matches[1]), ...$arguments);
        }

        throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name);
    }

    public function afterBuild($item): void
    {
    }

    final public function build(): mixed
    {
        $this->data += $this->generateValues();
        $item = $this->doBuild();
        $this->afterBuild($item);
        $this->reset();
        return $item;
    }

    /**
     * @throws ReflectionException
     */
    public function fromArray(array $payload): static
    {
        $this->reset();
        $this->normalize($payload);
        $this->data = array_merge($this->data, $payload);

        return $this;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    final public function reset(): static
    {
        $this->data = [];
        return $this;
    }

    final protected function with(string $key, $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    final protected function data(): array
    {
        return $this->data;
    }

    /**
     * @throws ReflectionException
     */
    protected function doBuild(): mixed
    {
        return $this->reflectionService->newInstanceWithProperties(static::returnType(), $this->data);
    }

    abstract protected function generateValues(): array;

    final protected function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @throws ReflectionException
     */
    protected function normalize(array &$payload): void
    {
        $fakeData = $this->generateValues();
        foreach ($payload as $key => $value) {
            if (null === $value) {
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $fakeValue = $fakeData[$key] ?? null;

            if (!is_object($fakeValue) && null !== $fakeValue) {
                continue;
            }

            $class = is_object($fakeValue)
                ? get_class($fakeValue)
                : $this->reflectionService->type(static::returnType(), $key);

            if (!class_exists($class)) {
                continue;
            }

            if (is_array($value) && $this->isAssoc($value)) {
                $mother = $this->resolveMother($class);
                $payload[$key] = null === $mother
                    ? $this->reflectionService->newInstanceWithProperties($class, $value)
                    : $mother->fromArray($value)->build();
                continue;
            }

            $payload[$key] = new $class($value);
        }
    }

    private function resolveMother(string $className): ?Mother
    {
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $propertyValue = $this->reflectionService->getValue($this, $property->getName());
            if (!$propertyValue instanceof self) {
                continue;
            }

            if ($propertyValue::returnType() === $className) {
                return $propertyValue;
            }
        }

        return null;
    }

    private function isAssoc(array $arr): bool
    {
        return !([] === $arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }
}
