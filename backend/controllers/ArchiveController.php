<?php
/**
 * 归档
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-26
 * Time: 下午3:37
 */

namespace backend\controllers;


use common\models\Article;
use Yii;
use yii\web\NotFoundHttpException;

class ArchiveController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'pageCache' => [
				'class' => 'yii\filters\PageCache',
				'only' => ['index', 'list'],
				'duration' => 0,
				'enabled' => true,
				'variations' => [
					Yii::$app->request->get('year', 1),
					Yii::$app->request->get('month', 0),
					Yii::$app->request->get('page', 0),
				],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => "SELECT COUNT(*) FROM x_article where is_delete = 0 and is_show = 1",
				],
			],
		];
	}
	
	/**
	 * Displays Archive Content.
	 * @return string
	 */
	public function actionIndex()
	{
		$list = Article::find()
			->select([
				'count' => 'count(1)',
				'date' => 'FROM_UNIXTIME(created_at,"%Y-%m")',
			])
			->where(['is_show' => Article::IS_SHOW_YES, 'is_delete' => Article::IS_DELETE_NO])
			->groupBy('date')
			->orderBy(['date' => SORT_DESC])
			->asArray()
			->all();
		
		$archiveList = [];
		foreach ($list as $item) {
			list ($year) = explode('-', $item['date']);
			$archiveList[ $year ][] = $item;
		}
		
		return $this->render('index', [
			'archiveList' => $archiveList
		]);
		
	}
	
	/**
	 * 归档文章列表
	 * @param int $year
	 * @param int $month
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionList(int $year, int $month)
	{
		$startAt = strtotime($year . '-' . $month);
		if (empty($startAt)) {
			throw new NotFoundHttpException('页面未找到～');
		}
		$endAt = strtotime(date('Y-m-t 23:59:59', $startAt));
		$where = [
			'and',
			['>=', 'created_at', $startAt],
			['<=', 'created_at', $endAt],
		] ;
		$data = $this->getArticleList($where);
		
		$breadcrumb = [
			[
				'name' => '首页',
				'href' => '/',
			],
			[
				'name' => '归档',
				'href' => "/archive.html",
			],
			[
				'name' => date('Y-m', $startAt),
				'href' => "/article/". date('Y/m', $startAt) .".html",
			],
		];
		
		$data['breadcrumb'] = $breadcrumb;
		$data['active_menu'] = 'archive';
		
		return $this->render('//site/index', $data);
	}
	
	public function afterAction($action, $result)
	{
		$result = $this->renderContentFilter($result);
		return parent::afterAction($action, $result);
	}
}