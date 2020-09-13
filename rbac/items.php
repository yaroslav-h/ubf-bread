<?php
return [
    'user' => [
        'type' => 1,
        'ruleName' => 'userGroup',
    ],
    'moder' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'user',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'moder',
        ],
    ],
];
