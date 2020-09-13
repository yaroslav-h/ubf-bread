<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => env('DB_DSN'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => env('DB_CHARSET', 'utf8'),
    // Schema cache options (for production environment)
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 3600,
    //'schemaCache' => 'cache',
    'attributes'=> [
        // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
    ]
];
