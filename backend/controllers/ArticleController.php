<?php
/**
 *
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-25
 * Time: 下午3:37
 */

namespace backend\controllers;


use common\exceptions\ParameterException;
use common\models\Article;
use common\models\ArticleComment;
use Yii;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;

class ArticleController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'PageCache' => [
				'class' => '\common\helpers\ArticlePageCache',
				'only' => ['index'],
				'duration' => 3600,
				'enabled' => YII_ENV == 'prod',
				'variations' => [
					Yii::$app->request->isAjax,
					Yii::$app->request->get('page', 1),
					Yii::$app->request->get('id', 0),
					Yii::$app->request->get('debug', 0),
				],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => "SELECT `updated_at` FROM x_article where id = :id",
					'params' => [
						':id' => ((int) Yii::$app->request->get('id', 0)),
					]
				],
			],
		];
	}
	/**
	 * Displays Article Content.
	 * @param int $id
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionIndex(int $id)
	{
		$this->layout = 'article-main';
		// 获取评论
		if (Yii::$app->request->isAjax) {
			$page = Yii::$app->request->get('page', 0);
			if ($page > 0) {
				return $this->getComments();
			}
		}
		
		$article = Article::findOne($id);
		if (empty($article)) {
			throw new NotFoundHttpException('该文章不存在');
		}
		
		Yii::$app->db->createCommand()->update(Article::tableName(), ['hits' => $article->hits+1], 'id='.$article->id)->execute();
		
		$breadcrumb = $this->getArticleBreadcrumb($article);
		
		// 上一条数据
		$prevArticle = Article::find()
			->select(['id', 'title'])
			->where(['>', 'created_at', $article->created_at])
            ->andWhere(['is_delete' => Article::IS_DELETE_NO, 'is_show' => Article::IS_SHOW_YES])
            ->limit(1)
            ->orderBy('created_at desc')
			->asArray()
			->one();
		
		// 下一条数据
		$nextArticle = Article::find()
			->select(['id', 'title'])
            ->where(['<', 'created_at', $article->created_at])
            ->andWhere(['is_delete' => Article::IS_DELETE_NO, 'is_show' => Article::IS_SHOW_YES])
			->limit(1)
            ->orderBy('created_at desc')
			->asArray()
			->one();
		
		if (empty($article->content->directory))  {
			$this->layout = 'main';
		}
		
		return $this->render('index', [
			'article' => $article,
			'breadcrumb' => $breadcrumb,
			'prevArticle' => $prevArticle,
			'nextArticle' => $nextArticle,
		]);
		
	}
	
	/**
	 * 发布评论
	 */
	public function actionRelease()
	{
		// 验证留言间隔, 1分钟内只能发一次
		$session = Yii::$app->session;
		$session->open();
		$lastPubAt = $session->get('comment_last_pub_at', 0);
		
		if ($lastPubAt > 0 && ($lastPubAt + 60) > time()) {
			exit("<script>alert('1分钟内只能发布一次哦!');history.go(-1)</script>");
		}
		$params = Yii::$app->request->post();
		$articleId = (int) ($params['article_id'] ?? 0);
		$article = Article::findOne($articleId);
		if (empty($article)) {
			throw new NotFoundHttpException('该文章不存在!');
		}
		if ($article->is_allow_comment == Article::IS_DELETE_NO) {
			exit("<script>alert('该篇文章不支持评论!');history.go(-1)</script>");
		}
		
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$comment = new ArticleComment();
			$comment->article_id = $articleId;
			$comment->avatar = sprintf('/uploads/avatar/%d.jpg', rand(1, 5));
			$comment->nickname = HtmlPurifier::process(trim($params['nickname'] ?? ''));
			$comment->email = HtmlPurifier::process(trim($params['email'] ?? ''));
			$comment->content = trim($params['content'] ?? '');
			$comment->ip = Yii::$app->request->userIP;
			
			if (!$comment->save()) {
				$error = current($comment->getFirstErrors());
				throw new ParameterException(ParameterException::INVALID, $error);
			}
			
			$article->comment_count ++;
			$article->scenario = 'update-comment_count';
			if (!$article->save()) {
				throw new ParameterException(ParameterException::INVALID, '发布失败');
			}
			
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			exit("<script>alert('". $e->getMessage() ."');history.go(-1)</script>");
		}
		
		$session->set('comment_last_pub_at', time());
		
		exit("<script>alert('发布成功');location.href='/article-".$articleId.".html'</script>");
	}
	
	/**
	 * 获取文章评论列表
	 * @return string
	 */
	public function getComments()
	{
		$this->layout = false;
		
		$articleId = (int) Yii::$app->request->get('id', 0);
		
		$query = ArticleComment::find()->where(['article_id' => $articleId]);
		
		list ($count, $pages) = $this->getPage($query, 30);
		
		$commentList = $query->asArray()
			->all();

		return $this->renderContentFilter($this->render('get-comments', [
			'commentList' => $commentList,
			'pages' => $pages,
		]));
	}
	
	/**
	 *
	 * @param Article $article
	 * @return array
	 */
	private function getArticleBreadcrumb($article)
	{
		
		return array_merge([
			[
				'name' => '首页',
				'href' => '/',
			]
		], $this->getAllCategoryBreadcrumb($article->category_id),
			[
				[
					'name' => $article->title,
					'href' => "/article-{$article->id}.html",
				],
			]
		);
	}
}