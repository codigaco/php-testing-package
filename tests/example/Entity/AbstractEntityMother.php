<?php

declare(strict_types=1);

namespace Codigaco\Testing\Tests\Example\Entity;

use Codigaco\Testing\Mother;

/**
 * @covers \Codigaco\Testing\Example\Entity\AbstractEntity
 * @method $this withId(string $value)
 * @method $this withVersion(int $value)
 */
abstract class AbstractEntityMother extends Mother
{
    protected function generateValues(): array
    {
        return [
            'id' => $this->faker->uuid,
            'version' => $this->faker->numberBetween(0, 100),
        ];
    }
}
