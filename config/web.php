<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'maxLogFiles' => 2,
                    'exportInterval' => 1,
                    'logVars' => ['_GET', '_POST']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['controllers'], //категория логов
                    'logFile' => '@runtime/logs/controllers.log', //куда сохранять
                    'logVars' => [] //не добавлять в лог глобальные переменные ($_SERVER, $_SESSION...)
                ],
            ],
        ],
        'helpers' => [
            'class' => 'app\components\Helpers',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'XzlgQHK7JWwhKwvS4E_vZRAObjvva5Ge',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            //'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mail.ru',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => 'yeticave@inbox.ru',
                'password' => 'Pd7YqbQcdMFJZXBqjxUp',
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        // использование человекопонятных URL
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'task/<id:\d+>' => 'tasks/view',
                'my-tasks/<code:\w+>&<page:\d+>' => 'tasks/my-tasks',
                'user/<id:\d+>' => 'users/view',
                'category/<id:\d+>&<page:\d+>' => 'tasks/category',
                'logon' => 'auth/logon',
                'registration' => 'auth/registration',
                'logout' => 'auth/logout',
                'site' => 'site/site',
                'add-task' => 'tasks/add-task',
                'accept/<id:\d+>' => 'tasks/accept',
                'reject/<id:\d+>' => 'tasks/reject',
                'edit-profile/<id:\d+>' => 'users/edit-profile',
                'cancel/<id:\d+>' => 'tasks/cancel',
                'tasks/<page:\d+>' => 'tasks/index',
                'contact' => 'site/contact',
                'download/<docId:\d+>' => 'tasks/download',
                'change-password/<id:\d+>' => 'users/change-password',
            ],
        ],
        'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    'class'        => 'yii\authclient\clients\VKontakte',
                    'clientId'     => $params['vkClientId'],
                    'clientSecret' => $params['vkClientKey'],
                    'scope' => 'email'
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'templatePath' => '@app/fixtures/templates',
            'fixtureDataPath' => '@app/fixtures/data',
            'namespace' => 'app\fixtures',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
