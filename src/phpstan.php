<?php

// Configuration to increase memory limit for PHPStan
ini_set('memory_limit', '256M');

return [
    'parameters' => [
        'level' => 4,
        'paths' => [
            __DIR__.'/app/',
        ],
        'excludePaths' => [
            __DIR__.'/storage/',
            __DIR__.'/vendor/',
            __DIR__.'/public/',
            __DIR__.'/resources/',
            __DIR__.'/bootstrap/',
            __DIR__.'/config/',
            __DIR__.'/database/',
            __DIR__.'/routes/',
            __DIR__.'/tests/',
        ],
    ],
];
