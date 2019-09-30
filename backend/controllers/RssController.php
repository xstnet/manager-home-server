<?php
/**
 * Rss
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-25
 * Time: 下午3:37
 */

namespace backend\controllers;


use common\helpers\Rss;
use common\models\Article;
use Yii;

class RssController extends BaseController
{
	public function behaviors()
	{
		return [
			'PageCache' => [
				'class' => 'yii\filters\PageCache',
				'only' => ['index'],
				'duration' => 0,
				'enabled' => true,
				'variations' => [],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => "SELECT COUNT(*) FROM x_article where is_delete = 0 and is_show = 1",
				],
			],
		];
	}
	
	public function actionIndex()
	{
		$rss = new Rss();
		
		$articleList = Article::find()
			->select(['id', 'title', 'created_at', 'description'])
			->where(['is_delete' => Article::IS_DELETE_NO, 'is_show' => Article::IS_SHOW_YES])
			->orderBy(['created_at' => SORT_DESC])
			->asArray()
			->all();
		
		foreach ($articleList as $item) {
			$rss->setItem([
				'title' => $item['title'],
				'link' => Yii::$app->request->hostInfo . "/article-{$item['id']}.html",
				'description' => $item['description'],
				'pubDate' => date("D, d M Y H:i:s ", $item['created_at']) . "GMT",
				'guid' => Yii::$app->request->hostInfo . "/article-{$item['id']}.html",
			]);
		}
		$xmlString = $rss->renderRss();
		Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
		Yii::$app->response->content = $xmlString;
	}
}