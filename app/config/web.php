<?php

$params = require(__DIR__ . '/params.php');

$basePath = dirname(__DIR__);
$webroot = dirname($basePath);

$config = [
    'id'           => 'basic',
    'basePath'     => $basePath,
    'bootstrap'    => ['log'],
    'language'     => 'ru-RU',
    'runtimePath'  => $webroot . '/runtime',
    'vendorPath'   => $webroot . '/vendor',
    'defaultRoute' => 'default',
    'components'   => [
        'request'      => [
            'cookieValidationKey' => 'VODh2qVud2MB7kT8Hj0P9WS_IQPQpiON',
        ],
        'assetManager' => [
            'forceCopy' => true,
            'bundles'   => [
                'yii\web\JqueryAsset'          => [
                    'js' => [YII_DEBUG ? 'jquery.js' : 'jquery.min.js'],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [YII_DEBUG ? 'css/bootstrap.css' : 'css/bootstrap.min.css'],
                ],
            ],
        ],
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'         => [
            'identityClass'   => 'app\models\user\User',
            'enableAutoLogin' => true,
            'loginUrl'        => '/sign/in',
        ],
        'authManager'  => [
            'class' => 'app\models\user\AuthManager',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
        ],
        'mailer'       => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'           => require(__DIR__ . '/db.php'),
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                [
                    'class'       => 'yii\web\GroupUrlRule',
                    'prefix'      => 'admin',
                    'routePrefix' => 'admin',
                    'rules'       => [
                        '' => 'default/index',
                        [
                            'class'       => 'yii\web\GroupUrlRule',
                            'prefix'      => 'references',
                            'routePrefix' => 'references',
                            'rules'       => [
                                '<_c:[\w\-]+>' => '<_c>/index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'i18n'         => [
            'translations' => [
                '*' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap'  => [
                        'app'              => 'app.php',
                        'app/user'         => 'user.php',
                        'app/reference'    => 'reference.php',
                        'app/ymlimport'    => 'ymlimport.php',
                        'app/distribution' => 'distribution.php',
                    ],
                ],
            ],
        ],
        'session'      => [
            'class' => 'yii\web\DbSession',
        ],
    ],
    'params'       => $params,
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
