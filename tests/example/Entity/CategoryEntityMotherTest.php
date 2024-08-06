<?php

declare(strict_types=1);

namespace Codigaco\Testing\Tests\Example\Entity;

use Codigaco\Testing\Example\Entity\CategoryEntity;
use Codigaco\Testing\Tests\Example\Entity\Mother\CategoryEntityMother;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CategoryEntity::class)]
class CategoryEntityMotherTest extends TestCase
{
    private CategoryEntityMother $categoryEntityMother;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryEntityMother = new CategoryEntityMother();
    }

    public function testValidateCategoryHasSameAttributes(): void
    {
        $category = $this->categoryEntityMother
            ->withId('id')
            ->withName('name')
            ->build();

        self::assertEquals('id', $category->id());
        self::assertEquals('name', $category->name());
    }
}
