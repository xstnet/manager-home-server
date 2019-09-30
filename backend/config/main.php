<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
        'v1' => [
            'class' => 'backend\modules\v1\Module',
        ],
    ],
    'components' => [
		'assetManager' => [
			'basePath' => '@webroot/assets',
			'baseUrl' => '@web/backend/assets',
		],
        'request' => [
            'csrfParam' => '_csrf-avwd',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'backend-avwd',
        ],
		'log' => [
			// 假如 YII_DEBUG 开启则是3，否则是0。 这意味着，假如 YII_DEBUG 开启，每个日志消息在日志消息被记录的时候， 将被追加最多3个调用堆栈层级；假如 YII_DEBUG 关闭， 那么将没有调用堆栈信息被包含。
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning',],
					'logVars' => ['_GET', '_POST', ],
					'enableRotation' => true, //开启日志文件分段写入，默认每个文件大小为10M
					'maxFileSize' => 10240, // KB
					'maxLogFiles' => 10, // 最多允许分段10个文件 如： backend-2018-10.1.log, backend-2018-10.2.log
					'logFile' => sprintf("@backend/runtime/logs/backend-%s.log",date('Y-m')),
				],
			],
		],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'urlManager'=>[
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'suffix'=>'.html',
			'rules' => [
				"/" => "/site/index",
				"/gii" => "/index.php?r=gii",
			],
		],

	],
    'params' => $params,
];
