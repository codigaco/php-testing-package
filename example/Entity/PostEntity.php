<?php

declare(strict_types=1);

namespace Codigaco\Testing\Example\Entity;

final class PostEntity extends AbstractEntity
{
    public function __construct(
        string $id,
        public readonly string $title,
    ) {
        parent::__construct($id);
    }
}
