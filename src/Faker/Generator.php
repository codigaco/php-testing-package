<?php

declare(strict_types=1);

namespace Codigaco\Testing\Faker;

use Codigaco\Testing\Mother;
use DateTime;
use DateTimeImmutable;
use Faker\Generator as FakerGenerator;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

class Generator extends FakerGenerator
{
    public function dateTimeImmutable(...$attributes): DateTimeImmutable
    {
        if (empty($attributes)) {
            return DateTimeImmutable::createFromMutable($this->dateTime);
        }

        return DateTimeImmutable::createFromMutable($this->dateTime(...$attributes));
    }

    /**
     * @throws ReflectionException
     */
    public function itemOrNull($item)
    {
        $item = $this->randomElement([$item, null]);

        if ($item instanceof Mother) {
            return $item->build();
        }

        return $item;
    }

    /**
     * @throws ReflectionException
     */
    public function arrayOf(
        Mother|DateTime|DateTimeImmutable|string $item,
        int $minLength = 1,
        int $maxLength = null,
        ...$attributes
    ): array {
        $arrayLength = $this->calculateLength($minLength, $maxLength);
        $data = array_fill(0, $arrayLength, null);

        if ($item instanceof Mother) {
            return array_map(static function () use ($item) {
                return $item->reset()->build();
            }, $data);
        }

        if ($item instanceof DateTime) {
            return array_map(function () {
                return $this->dateTime;
            }, $data);
        }

        if ($item instanceof DateTimeImmutable) {
            return array_map(function () use ($attributes) {
                return $this->dateTimeImmutable(... $attributes);
            }, $data);
        }

        if (empty($attributes)) {
            try {
                return array_map(function () use ($item) {
                    return $this->$item;
                }, $data);
            } catch (InvalidArgumentException) {
            }
        }

        try {
            return array_map(function () use ($item, $attributes) {
                return $this->$item(... $attributes);
            }, $data);
        } catch (InvalidArgumentException) {
        }

        throw new RuntimeException('Uncontrolled item');
    }

    private function calculateLength(int $minLength, ?int $maxLength): int
    {
        if ($minLength <= 0) {
            $minLength = 1;
        }

        if (null === $maxLength || $maxLength <= $minLength) {
            return $minLength;
        }

        return $this->numberBetween($minLength, $maxLength);
    }
}
