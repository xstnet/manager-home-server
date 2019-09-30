<?php
/**
 * 物品管理
 * Created by PhpStorm
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 2019/9/26
 * Time: 10:48 下午
 */

namespace frontend\controllers;


use frontend\services\article\ArticleService;
use yii\filters\VerbFilter;
use Yii;

class ArticleController extends BaseController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'get-article-list' => ['get'],
						'create-article' => ['post'],
						'delete-article' => ['post'],
						'update-article' => ['post'],
					],
				],
			]
		);
	}


    public function actions()
    {
        return [];
    }

    public function actionGetArticleList()
    {
        $result = ArticleService::getInstance()->getArticleList();
        return static::returnJson($result);
    }

    /**
     * 添加分类
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreateArticle()
    {
        $params = static::postParams();
        $result = ArticleService::getInstance()->createArticle($params);

        return static::returnJson('添加成功', $result);
    }

}