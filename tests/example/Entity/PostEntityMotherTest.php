<?php

declare(strict_types=1);

namespace Codigaco\Testing\Tests\Example\Entity;

use Codigaco\Testing\Tests\Example\Entity\Mother\PostEntityMother;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostEntityMother::class)]
class PostEntityMotherTest extends TestCase
{
    private PostEntityMother $postEntityMother;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postEntityMother = new PostEntityMother();
    }

    public function testValidatePostHasSameAttributes(): void
    {
        $post = $this->postEntityMother
            ->withId('id')
            ->withTitle('title')
            ->withVersion(1)
            ->build();

        self::assertEquals('id', $post->id);
        self::assertEquals('title', $post->title);
        self::assertEquals(1, $post->version());
    }

    public function testValidateRandomPost(): void
    {
        $post = $this->postEntityMother
            ->build();

        self::assertIsString($post->id);
        self::assertNotEmpty($post->id);
        self::assertIsString($post->title);
        self::assertNotEmpty($post->title);
    }
}
