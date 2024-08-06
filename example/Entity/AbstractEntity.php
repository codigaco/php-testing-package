<?php

declare(strict_types=1);

namespace Codigaco\Testing\Example\Entity;

abstract class AbstractEntity
{
    public function __construct(
        public readonly string $id,
        private int $version = 0,
    ) {
    }

    public function version(): int
    {
        return $this->version;
    }
}
