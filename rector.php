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
        LevelSetList::UP_TO_PHP_82
    ]);

    $rectorConfig->skip([
        // Skipping LongArrayToShortArrayRector, because it transforms long multi-line
        // array to one line arrays. This would destroy readability for now.
        Rector\Php54\Rector\Array_\LongArrayToShortArrayRector::class,

        // Skipping PowToExpRector, because pow is not deprecated or has any disadvantage
        // over **
        // See https://www.php.net/manual/en/function.pow.php
        Rector\Php56\Rector\FuncCall\PowToExpRector::class,

        // Skipping Constructor Promotion right now, because it is a great feature and syntactic sugar.
        // However, to keep the changeset small, we might introduce this at a later stage.
        // More info:
        //  - https://wiki.php.net/rfc/constructor_promotion
        //  - https://github.com/php/php-src/pull/5291
        Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,

        // Skipping strict string defined function call args right now, because the changelog is too big.
        // Right now, we did not see an issue with this yet.
        // However, to keep the changeset small, we might introduce this at a later stage.
        // More info:
        //  - https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#nulltostrictstringfunccallargrector
        Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector::class,
    ]);
};
