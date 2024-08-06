<?php

declare(strict_types=1);

namespace Codigaco\Testing\Faker;

use Faker\Factory as FakerFactory;

class Factory extends FakerFactory
{
    public const DEFAULT_LOCALE = 'es_ES';

    public static function create($locale = self::DEFAULT_LOCALE): Generator
    {
        $generator = new Generator();
        foreach (static::$defaultProviders as $provider) {
            $providerClassName = self::getProviderClassname($provider, $locale);
            $generator->addProvider(new $providerClassName($generator));
        }

        return $generator;
    }
}
