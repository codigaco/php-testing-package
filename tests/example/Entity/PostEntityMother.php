<?php

declare(strict_types=1);

namespace Codigaco\Testing\Tests\Example\Entity;

use Codigaco\Testing\Example\Entity\PostEntity;

/**
 * @covers \Codigaco\Testing\Example\Entity\PostEntity
 * @method PostEntity build()
 * @method $this withTitle(string $value)
 */
class PostEntityMother extends AbstractEntityMother
{
    public static function returnType(): string
    {
        return PostEntity::class;
    }

    protected function generateValues(): array
    {
        return parent::generateValues() +
            [
                'title' => $this->faker->title,
            ];
    }
}
