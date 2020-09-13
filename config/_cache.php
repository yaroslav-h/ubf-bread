<?php

return [
    'class' => \yii\redis\Cache::class,
    'redis' => [
        'class' => yii\redis\Connection::class,
        'hostname' => env('REDIS_HOST'),
        'port' => env('REDIS_PORT'),
        'database' => 1,
    ]
];