<?php
/**
 * Desc: 用户管理
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午3:56
 */

namespace frontend\controllers;

use common\exceptions\ParameterException;
use frontend\services\user\UserService;
use yii\filters\VerbFilter;
use Yii;

class UserController extends BaseController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'register' => ['post'],
//						'get-user-info' => ['get'],
					],
				],
			]
		);
	}

	public function actions()
	{
		return [

		];
	}

	public function actionRegister()
    {
        $params = self::postParams();
        $data = UserService::getInstance()->register($params);
        return static::returnJson('注册成功', $data);
    }

    /**
     * Get User Info
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetUserInfo()
    {
        $data = UserService::getInstance()->getUserInfo();
        return static::returnJson($data);
    }

	/**
	 * @Desc: 个人信息，页面
	 * @return string
	 */
	public function actionProfile()
	{

		return $this->render('profile', [
		]);
	}

	/**
	 * @Desc: 更新个人信息
	 */
	public function actionSaveUserProfile()
	{
		$params = Yii::$app->request->post();
		$user = AdminUser::findOne(Yii::$app->user->id);
		if (empty($user)) {
			throw new ParameterException(ParameterException::INVALID, '用户不存在');
		}
		$user->avatar = $params['avatar'];
		if (empty($params['nickname'])) {
			throw new ParameterException(ParameterException::INVALID, '昵称不能为空');
		}
		if (!empty($params['password'])) {
			$user->setPassword($params['password']);
		}
		$user->email = $params['email'];
		$user->nickname = $params['nickname'];
		$user->saveModel();

		return self::ajaxSuccess('更新信息成功');
	}

}