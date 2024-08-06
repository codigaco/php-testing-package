<?php

declare(strict_types=1);

namespace Codigaco\Testing\Tests\Example\Entity\Mother;

use Codigaco\Testing\Example\Entity\CategoryEntity;
use Codigaco\Testing\Mother;

/**
 * @method CategoryEntity build()
 * @method $this withId(string $value)
 * @method $this withName(string $value)
 */
class CategoryEntityMother extends Mother
{
    public static function returnType(): string
    {
        return CategoryEntity::class;
    }

    protected function generateValues(): array
    {
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->word,
        ];
    }
}
