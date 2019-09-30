<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
	'defaultRoute' => 'site/index',
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
		'assetManager' => [
			'basePath' => '@webroot/assets',
			'baseUrl' => '@web/assets',
		],
        'request' => [
            'csrfParam' => '_csrf_token_frontend_xstnet',
            'enableCsrfValidation' => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
			'loginUrl' => 'login/index',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning', ],
					'logVars' => ['_GET', '_POST', ],
					'enableRotation' => true, //开启日志文件分段写入，默认每个文件大小为10M
					'maxFileSize' => 10240, // KB
					'maxLogFiles' => 10, // 最多允许分段10个文件 如： frontend-2018-10.1.log, frontend-2018-10.2.log
					'logFile' => sprintf("@frontend/runtime/logs/frontend-%s.log",date('Y-m')),
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'error/error',
		],


        'urlManager'=>[
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                "/" => "/site/index",
                "/gii" => "/index.php?r=gii",
                "/login" => "login/login",
            ],
        ],
		'response' => [
			'class' => 'yii\web\Response',
			'on beforeSend' => function ($event) {
				// 返回的错误信息只显示code和message
				$response = $event->sender;
				// 业务逻辑错误
				if (isset($response->data['code']) && $response->data['code'] !== 0) {
					$response->data = [
						'code' => $response->data['code'],
						'message' => $response->data['message'],
					];
				}
				// HTTP错误
//				if (isset($response->data['status']) && $response->data['status'] !== 200) {
//					$response->data = [
//						'code' => $response->data['status'],
//						'message' => $response->data['message'],
//					];
//				}
				$response->statusCode = 200; // 错误信息返回码同样200
			},
		],

    ],
    'params' => $params,
];
