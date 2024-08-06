<?php

namespace Codigaco\Testing;

use PHPUnit\Framework\Assert as PHPUnitAssert;

class Assert extends PHPUnitAssert
{
    /**
     * @param array $expected
     * @param array $actual
     * @param bool $strict
     * @param string $message
     */
    public static function assertArraySubset($expected, $actual, bool $strict = false, string $message = ''): void
    {
        if (method_exists(parent::class, 'assertArraySubset')) {
            parent::assertArraySubset($expected, $actual, $strict, $message);
            return;
        }

        foreach ($expected as $key => $value) {
            self::assertArrayHasKey($key, $actual, $message);
            is_array($value)
                ? self::assertArraySubset($value, $actual[$key], $strict, $message . '[' . $key . ']')
                : self::assertEquals($value, $actual[$key], $message . '[' . $key . ']');
        }
    }
}
