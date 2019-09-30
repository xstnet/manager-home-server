<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/9/10
 * Time: 18:26
 */

namespace frontend\controllers;

use common\exceptions\ParameterException;
use common\models\User;
use common\models\UserHome;
use common\utils\JwtUtil;
use Yii;
use yii\filters\VerbFilter;


class LoginController extends BaseController
{

	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'login' => ['post'],
						'logout' => ['get',],
					],
				],
			]
		);
	}


	/**
	 * @Desc: 登录
	 * @throws ParameterException
	 * @throws \common\exceptions\DatabaseException
	 */
	public function actionLogin()
	{
		$username = trim(Yii::$app->request->post('username', ''));
		$password = trim(Yii::$app->request->post('password', ''));

		$user = self::validateLogin($username, $password);
		$time = time();
		$user->login_count ++;
		$user->last_login_at = $user->login_at ? : $time;
		$user->login_at = $time;
		$user->last_login_ip = $user->login_ip ? : Yii::$app->request->userIP;
		$user->login_ip = Yii::$app->request->getUserIP();
		$user->saveModel();
        $tokenData = [
            'user_id' => $user->id,
            'home_id' => UserHome::find(['user_id' => $user->id, 'is_default' => UserHome::IS_DEFAULT_YES])->select('home_id')->scalar(),
            'username' => $user->username
        ];
        $token = JwtUtil::getTokenByKey($tokenData);

        return static::returnJson('登录成功', ['token' => $token, 'home_id' => $tokenData['home_id']]);
	}


	/**
	 * @Desc:
	 * @param $username
	 * @param $password
	 * @return User
	 * @throws ParameterException
	 */
	protected static function validateLogin($username, $password)
	{
		if (empty($username)) {
			throw new ParameterException(ParameterException::INVALID, '请输入用户名!');
		}
		if (empty($password)) {
			throw new ParameterException(ParameterException::INVALID, '请输入密码!');
		}
		$user = User::findByUsername($username);
		if (empty($user)) {
			throw new ParameterException(ParameterException::INVALID, '用户不存在!');
		}
		if (!$user->validatePassword($password)) {
			throw new ParameterException(ParameterException::INVALID, '密码错误!');
		}
		if ($user->status == User::STATUS_DISABLED) {
			throw new ParameterException(ParameterException::INVALID, '账号已被禁用!');
		}

		return $user;
	}

	/**
	 * Logout action.
	 *
	 * @return string
	 */
	public function actionLogout()
	{
		AdminLogController::saveAdminLog();
		Yii::$app->user->logout();

		return $this->goHome();
	}
}