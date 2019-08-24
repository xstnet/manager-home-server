<?php
namespace frontend\controllers;

use common\models\Article;
use common\models\Messages;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends BaseController
{
	// 关于我的文章ID
	const ABOUT_ARTICLE_ID = 10;
	
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
			'pageCache' => [
				'class' => 'yii\filters\PageCache',
				'only' => ['index', 'category', 'tag'],
				'duration' => 3600,
				'variations' => [
					Yii::$app->request->get('page', 1),
					Yii::$app->request->get('categoryId', 0),
					Yii::$app->request->get('s', ''),
					Yii::$app->request->get('tag', ''),
					Yii::$app->request->get('debug', 0),
				],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => 'SELECT MAX(`updated_at`) FROM x_article',
				],
				'enabled' => YII_ENV == 'prod',
			],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
    	$data = $this->getArticleList();
    	
		return $this->render('index', $data);
	}
	
	/**
	 * 分类文章列表
	 * @param int $categoryId
	 * @return string
	 */
	public function actionCategory(int $categoryId)
	{
		$parents = Yii::$app->userCache->get('articleCategory')[$categoryId]['parents'];
//		$data = $this->getArticleList("find_in_set(category_id, '$parents')");
		$data = $this->getArticleList(['category_id' => $categoryId]);
		
		$breadcrumb = array_merge([
			[
				'name' => '首页',
				'href' => '/',
			]
		], $this->getAllCategoryBreadcrumb($categoryId)
		);
		
		$data['breadcrumb'] = $breadcrumb;
		$data['active_menu'] = '';
		
		return $this->render('index', $data);
	}
	
	/**
	 * 搜索
	 * @return string
	 */
	public function actionSearch()
	{
		$keyword = \yii\helpers\Html::encode(trim(Yii::$app->request->get('s', '')));

		if (empty($keyword)) {
			$keyword = trim(Yii::$app->request->get('keyword', ''));
		}
		$where = [];
		if (!empty($keyword)) {
			$where = ['like', 'title', $keyword];
		}
		
		$data = $this->getArticleList($where);
		
		$breadcrumb = [
			[
				'name' => '首页',
				'href' => '/',
			],
			[
				'name' => "搜索： $keyword ",
				'href' => false,
			],
			[
				'name' => "共搜索到 <strong>{$data['count']}</strong> 条数据",
				'href' => false,
			]
		];
		
		$data['breadcrumb'] = $breadcrumb;
		$data['active_menu'] = '';
		
		foreach ($data['articleList'] as $key => $item) {
			$data['articleList'][$key]['title'] = str_replace($keyword, "<span style='color: #d62929'>$keyword</span>", $item['title']);
		}
		
		return $this->render('index', $data);
	}
	
	public function actionTag($tag)
	{
		$tag = \yii\helpers\Html::encode($tag);
		$where = new \yii\db\Expression('FIND_IN_SET(:field, keyword)',[':field' => $tag]);
		$data = $this->getArticleList($where);
		
		$breadcrumb = [
			[
				'name' => '首页',
				'href' => '/',
			],
			[
				'name' => $tag,
				'href' => "/tag/$tag.html",
			],
			[
				'name' => "共查找到 <strong>{$data['count']}</strong> 条数据",
				'href' => false,
			]
		];
		
		$data['breadcrumb'] = $breadcrumb;
		$data['active_menu'] = '';
		
		return $this->render('index', $data);
	}
	
	public function actionCounter()
	{
		$this->dayCount();
	}
	
	public function actionAbout()
	{
		$breadcrumb = [
			[
				'name' => '首页',
				'href' => '/',
			],
			[
				'name' => '关于我',
				'href' => '/about.html',
			]
		];
		
		$data['breadcrumb'] = $breadcrumb;
		$data['active_menu'] = 'about';
		
		$query = Messages::find()->orderBy('id desc');
		
		list ($count, $pages) = $this->getPage($query, 30);
		
		$messageList = $query->asArray()
			->all();
		
		$data['messageList'] = $messageList;
		$data['pages'] = $pages;
		$data['article'] = Article::findOne(static::ABOUT_ARTICLE_ID);
		
		return $this->render('about', $data);
	}
	
    
	
    public function afterAction($action, $result)
	{
		$result = $this->renderContentFilter($result);
		return parent::afterAction($action, $result);
	}
	
}
