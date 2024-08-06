<?php

namespace Codigaco\Testing;

interface MotherInterface
{
    /** @return string<class-string> */
    public static function returnType(): string;

    /** @return static */
    public function fromArray(array $payload);

    public function build();
}
