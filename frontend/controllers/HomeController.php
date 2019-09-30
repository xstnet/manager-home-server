<?php
/**
 * Desc: 我的家
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/06/25
 * Time: 15:30
 */

namespace frontend\controllers;


use frontend\services\home\HomeService;
use yii\filters\VerbFilter;
use Yii;

class HomeController extends BaseController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'get-room-list' => ['get', ],
						'create-home' => ['post', ],
					],
				],
			]
		);
	}

	public function actions()
	{
		return [];
	}

	/* *************************   角色管理  *********************××****************************************************/

    /**
     * 获取房间列表
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
	public function actionGetRoomList()
	{
		$result = HomeService::getInstance()->getRoomList();
		return static::returnJson($result);
	}

	public function actionCreateRoom()
    {
        $params = static::postParams();
        $result = HomeService::getInstance()->createRoom($params);

        return static::returnJson('添加成功', $result);
    }

    /**
     * 添加家具
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
	public function actionCreateFurniture()
    {
        $params = static::postParams();
        $result = HomeService::getInstance()->createFurniture($params);

        return static::returnJson('添加成功', $result);
    }

}