<?php
namespace frontend\controllers;

use common\models\Article;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * Site controller
 */
class BaseController extends Controller
{
	/**
	 *
	 * @param \yii\db\ActiveQuery $query
	 * @param int $pageSize
	 * @return array
	 */
	public function getPage($query, $pageSize = 10)
	{
		$page = (int) Yii::$app->request->get('page', 1);
		$page = $page < 1 ? 1 : $page;
		$count = $query->count();
		
		$offset = ($page - 1) * $pageSize;
		
		$query->offset($offset)->limit($pageSize);
		
		$pages = new Pagination(['totalCount' => $count]);
		
		return [$count, $pages];
	}
	
	/**
	 * 二次处理返回的Html
	 * @param string $content
	 * @return string
	 */
	public function renderContent($content)
	{
		return $content = parent::renderContent($content);
		
		return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</","//","'/\*[^*]*\*/'","/\r\n/","/\n/","/\t/",'/>[ ]+</'),array(">\\1<",'','','','','','><'), $content)));
	}
	
	/**
	 * 获取文章列表
	 * @param $where
	 * @return array
	 */
	protected function getArticleList($where = []) : array
	{
		$query = Article::find()
			->where(['is_show' => Article::IS_SHOW_YES, 'is_delete' => Article::IS_DELETE_NO])
			->andWhere($where);
		list ($count, $pages) = $this->getPage($query);
		$articleList = $query->orderBy(['created_at' => SORT_DESC])
			->asArray()
			->all();
		
		return [
			'articleList' => $articleList,
			'pages' => $pages,
			'count' => $count,
		];
	}
}