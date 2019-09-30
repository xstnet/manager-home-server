<?php

namespace frontend\controllers;


use yii\filters\AccessControl;
use Yii;

class ErrorController extends BaseController
{
	const SYSTEM_ERROR = 10000;

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['error'],
						'allow' => true,
					],
				],
			],
		];
	}

	public function actions()
	{
		return [
			'error' => [
				'class' => 'common\actions\ErrorAction', // 错误处理
			],
		];
	}
}
