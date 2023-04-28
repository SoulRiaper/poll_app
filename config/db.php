<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost:3306;dbname=sys',
    'username' => 'root',
    'password' => '1001',
    'charset' => 'utf8mb4',

    /* for docker containers network */
//     'class' => 'yii\db\Connection',
//     'dsn' => 'mysql:host=pass-mysql-id-container:3306;dbname=sys',
//     'username' => 'root',
//     'password' => '1001',
//     'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
