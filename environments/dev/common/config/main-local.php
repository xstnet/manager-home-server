<?php
return [
    'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=127.0.0.1;dbname=manager-home',
			'username' => 'root',
			'password' => 'root',
			'charset' => 'utf8',
			'tablePrefix' => 'mh_',
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => false,
			'transport' => [
				//这里如果你是qq的邮箱，可以参考qq客户端设置后再进行配置 http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.qq.com',
				// qq邮箱
				'username' => 'shantongxu@qq.com',
				//授权码, 什么是授权码， http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
				'password' => '',
				'port' => '25',
				'encryption' => 'tls',
			],
			'messageConfig' => [
				'charset'=>'UTF-8',
				'from'=>['notifications@xstnet.com	' => '徐善通博客']
			],
		],
		
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@backend/runtime/cache',
        ],
//		'cache' => [
//			'class' => 'common\helpers\RedisCache',
//		],
		'userCache' => [
			'class' => 'common\helpers\UserCache',
		],
//		'redis' => [
//			'class' => 'yii\redis\Connection',
//			'hostname' => '127.0.0.1',
//			'port' => 6379,
//			'database' => 1,
//			'password' => '123456789'
//		],
    ],
];
