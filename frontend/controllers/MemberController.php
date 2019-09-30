<?php
/**
 * Desc: 权限管理-用户
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午3:56
 */

namespace frontend\controllers;

use frontend\services\member\MemberService;
use frontend\services\permission\PermissionService;
use yii\filters\VerbFilter;
use Yii;

class MemberController extends AdminLogController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'get-user-info' => ['get'],
						'get-members' => ['get'],
						'change-status' => ['post'],
						'add-member' => ['post'],
						'save-member' => ['post'],
						'delete-member' => ['post'],
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

	/**
	 * @Desc: 用户管理，页面
	 * @return string
	 */
	public function actionIndex()
	{
		$roles = PermissionService::instance()->getRolesBriefList();
		return $this->render('index', [
			'roles' => $roles,
		]);
	}

	/**
	 * @Desc: 获取用户列表
	 * @return array
	 */
	public function actionGetMembers()
	{
		$ret = MemberService::instance()->getMemberList();
		return self::ajaxSuccess(self::AJAX_MESSAGE_SUCCESS, $ret);
	}

	/**
	 * @Desc: 更新用户状态
	 * @return array
	 */
	public function actionChangeStatus()
	{
		$id = Yii::$app->request->post('id', 0);
		MemberService::instance()->changeStatus($id);

		return self::ajaxSuccess('更新成功');
	}

	/**
	 * @Desc: 添加用户
	 * @return array
	 */
	public function actionAddMember()
	{
		$params = Yii::$app->request->post();
		MemberService::instance()->addMember($params);
		return self::ajaxSuccess('添加成功');
	}

	/**
	 * @Desc: 更新用户信息
	 * @return array
	 */
	public function actionSaveMember()
	{
		$params = Yii::$app->request->post();
		MemberService::instance()->saveMember($params);
		return self::ajaxSuccess('更新成功');
	}

	/**
	 * @Desc: 删除用户
	 * @return array
	 */
	public function actionDeleteMember()
	{
		$id = Yii::$app->request->post('id', 0);
		MemberService::instance()->deleteMember($id);

		return self::ajaxSuccess('删除成功');
	}

}