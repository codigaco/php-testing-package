<?php

declare(strict_types=1);

use DG\BypassFinals;

require_once __DIR__ . '/../vendor/autoload.php';

BypassFinals::denyPaths([
    '*/vendor/phpunit/*',
]);
DG\BypassFinals::enable();
