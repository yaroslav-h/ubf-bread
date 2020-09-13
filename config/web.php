<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$cookieParams = array_filter([
    'httpOnly' => true,
    //'domain'   => env('COOKIE_DOMAIN'),
    'secure'   => env('COOKIE_SECURE'),
    'sameSite' => 'Strict'
]);

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@res'   => '@app/modules/res',
        '@admin' => '@app/modules/admin',
    ],
    'on beforeRequest' => function () {
        if(request()->cookies->get('_locale')) {
            Yii::$app->language = request()->cookies->get('_locale')->value;
        }
    },
    'components' => [
        'request' => [
            'class' => 'app\components\Request',
            'cookieValidationKey' => env('APP_COOKIE_VALIDATION_KEY'),
            'csrfCookie' => array_merge($cookieParams, ['expire' => time()+31104000]),
            'trustedHosts' => [
                '0.0.0.0/0', // TODO ?!?!?!?!
            ],
        ],
        'cache' => require __DIR__ . '/_cache.php',
        'authManager' => require __DIR__ . '/_authManager.php',
        'user' => [
            'class' => \app\components\User::class,
            'identityCookie' => array_merge(['name' => '_identity'], $cookieParams),
        ],
        'session' => [
            'class' => app\components\Session::class,
            'name'  => '_session',
            'cookieParams' => array_merge($cookieParams, ['lifetime' => 31104000]),
        ],
        'errorHandler' => [
            'errorAction' => 'app/error',
        ],
        'mailer' => require __DIR__ . '/_mailer.php',
        'auth' => [
            'class' => yii\authclient\Collection::class,
            'clients' => [
                'google' => [
                    'class' => yii\authclient\clients\Google::class,
                    'clientId'     => env('GOOGLE_CLIENT_ID'),
                    'clientSecret' => env('GOOGLE_CLIENT_SECRET'),
                    'validateAuthState' => false,
                    'returnUrl'    => 'postmessage',
                ],
            ],
        ],
        'log' => require __DIR__ . '/_log.php',
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => 'uploads/<path:.*>',
                    'route' => 'glide/index',
                    'encodeParams' => false
                ],
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'admin\Module'
        ],
        '_' => [
            'class' => 'res\Module'
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [
                    'admin' => '@admin/templates/crud/default', // template name => path to template
                ]
            ]
        ],
    ];
}

return $config;
