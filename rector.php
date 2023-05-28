<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        // Excluding the directories that contain external source code
        //__DIR__ . '/ext_inc',
        //__DIR__ . '/ext_scripts',

        __DIR__ . '/inc',
        __DIR__ . '/modules',
        __DIR__ . '/tests',
    ]);

    // Register a single rule
    // $rectorConfig->rule(Rector\Php70\Rector\FuncCall\RandomFunctionRector::class);

    // Define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_71
    ]);

    $rectorConfig->skip([
        // Skipping LongArrayToShortArrayRector, because it transforms long multi-line
        // array to one line arrays. This would destroy readability for now.
        Rector\Php54\Rector\Array_\LongArrayToShortArrayRector::class,

        // Skipping PowToExpRector, because pow is not deprecated or has any disadvantage
        // over **
        // See https://www.php.net/manual/en/function.pow.php
        Rector\Php56\Rector\FuncCall\PowToExpRector::class,
    ]);
};
