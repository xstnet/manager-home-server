<?php
/**
 *
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-26
 * Time: 上午10:00
 */
namespace console\controllers;

use common\helpers\Helpers;
use common\models\ArticleContents;
use Yii;
use common\models\Article;
use common\models\ArticleTag;

class ArticleController extends BaseController
{
	public function actionInitArticleTag()
	{
		$articleList = Article::find()
			->select(['keyword', 'created_at', 'updated_at'])
			->where(['<>', 'keyword', ''])
			->orderBy(['created_at' => SORT_ASC])
			->asArray()
			->all();
		
		foreach ($articleList as $item) {
			if (empty(trim($item['keyword']))) {
				continue;
			}
			$tags = explode(',', trim($item['keyword']));
			foreach ($tags as $tag) {
				Yii::$app->db->createCommand()->upsert(ArticleTag::tableName(), [
					'name' => $tag,
					'article_count' => 1,
					'is_show' => ArticleTag::IS_SHOW_YES,
					'created_at' => $item['created_at'],
					'updated_at' => $item['updated_at'],
				], [
					'article_count' => new \yii\db\Expression('article_count + 1'),
				])->execute();
			}
		}
		
		echo '初始化标签完成' . PHP_EOL;
	}
	
	/**
	 * 生成文章目录
	 * php yii article/create-directory
	 */
	public function actionCreateDirectory()
	{
		$articleContents = ArticleContents::find()
//			->where(['directory' => ''])
			->batch(30);
		
		/**
		 * @var $item ArticleContents
		 */
		foreach ($articleContents as $items) {
			foreach ($items as $item) {
				list ($directory, $text) = Helpers::createArticleDirectory($item->content);
				$item->directory = $directory;
				$item->content = $text;
				$item->saveModel();
			}
		}

	}
}