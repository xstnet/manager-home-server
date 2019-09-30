<?php
/**
 * Desc: 缓存管理
 * Created by PhpStorm.
 * User: xstnet
 * Date: 19-07-26
 * Time: 下午12:56
 */

namespace frontend\controllers;

use common\exceptions\ParameterException;
use common\models\AdminUser;
use yii\filters\VerbFilter;
use Yii;

class CacheController extends AdminLogController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'index' => ['get'],
						'clear-index' => ['get'],
						'clear-all' => ['get'],
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
	 * @Desc: 个人信息，页面
	 * @return string
	 */
	public function actionIndex()
	{

		return $this->render('index', [
		]);
	}
	
	/**
	 * 清理首页缓存
	 * @return array
	 */
	public function actionClearIndex()
	{
		Yii::$app->userCache->refresh('setting');
		Yii::$app->userCache->refresh('articleCategory');
		Yii::$app->userCache->refresh('ArticleCategoryTree');
		Yii::$app->userCache->refresh('latestArticle');
		Yii::$app->userCache->refresh('tagList');
		
		$this->renderIndex();
		
		return self::ajaxSuccess('清理成功');
	}
	
	/**
	 * 清理全部缓存
	 * @return array
	 */
	public function actionClearAll()
	{
		Yii::$app->cache->useDefault();
		Yii::$app->cache->flush();
		
		$dir = Yii::getAlias('@frontend/runtime/cache');
		if (is_dir($dir)) {
			$this->removeDir($dir);
		}
		
		$this->renderIndex();
		
		return self::ajaxSuccess('清理成功');
	}

	private function renderIndex()
	{
		return true;
		$url = 'https://www.xstnet.com/index.php';
		if (YII_ENV == 'dev') {
			$url = 'http://yii-admin.com/index.php';
		}
		
		$arrContextOptions = [
			"ssl"=> [
				"verify_peer" => false,
				"verify_peer_name" => false,
			],
		];
		
		$indexHtml = file_get_contents($url, false, stream_context_create($arrContextOptions));
		$filepath = Yii::getAlias('@frontend/web/index.html');
		file_put_contents($filepath, $indexHtml);
	}
	
	public function removeDir($dirName)
	{
		if(!is_dir($dirName))
		{
			return false;
		}
		$handle = @opendir($dirName);
		while(($file = @readdir($handle)) !== false)
		{
			//判断是不是文件 .表示当前文件夹 ..表示上级文件夹 =2
			if($file != '.' && $file != '..')
			{
				$dir = $dirName . '/' . $file;
				is_dir($dir) ? $this->removeDir($dir) : @unlink($dir);
			}
		}
		closedir($handle);
		@rmdir($dirName);
	}

}