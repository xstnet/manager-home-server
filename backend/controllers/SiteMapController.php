<?php
/**
 * Site Map
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-26
 * Time: 下午3:37
 */

namespace backend\controllers;


use common\models\Article;
use Yii;

class SiteMapController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'pageCache' => [
				'class' => 'yii\filters\PageCache',
				'only' => ['index',],
				'duration' => 0,
				'enabled' => true,
				'variations' => [Yii::$app->language],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => "SELECT COUNT(*) FROM x_article",
				],
			],
		];
	}
	
	/**
	 * Displays Site Map Xml.
	 * @return string
	 */
	public function actionIndex()
	{
		$list = Article::find()
			->where(['is_show' => Article::IS_SHOW_YES, 'is_delete' => Article::IS_DELETE_NO])
			->orderBy(['created_at' => SORT_DESC])
			->asArray()
			->all();
		
		$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlString .= '<urlset>';
		foreach ($list as $item) {
			$loc = "<loc>https://www.xstnet.com/article-" . $item['id'] . ".html</loc>";
			$lastmod = "<lastmod>" . date('Y-m-d', $item['created_at']) . "</lastmod>";
			$xmlString .= sprintf("<url>%s%s</url>", $loc, $lastmod);
		}
		$xmlString .= '</urlset>';
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
		
		Yii::$app->response->content = $xmlString;
	}
}