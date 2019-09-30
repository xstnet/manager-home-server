<?php
/**
 * Created by PhpStorm.
 * User: xstnet
 * Date: 2018/11/3
 * Time: 19:58
 */

namespace common\helpers;

use backend\services\setting\SettingService;
use common\models\Article;
use common\models\ArticleCategory;
use common\models\ArticleTag;
use common\models\CountTotal;
use Yii;


class UserCache
{
	public function get($name)
	{
		$ret = Yii::$app->cache->get($name);
		if ($ret === false) {
			$funName = 'get' . ucfirst($name);
			if (method_exists ($this, $funName)) {
				$ret = $this->$funName();
			}
		}
		
		return $ret;
	}

	public function set($key, $value, $duration = null, $dependency = null)
	{
		Yii::$app->cache->set($key, $value, $duration = null, $dependency = null);
	}

	public function refresh($name)
	{
		Yii::$app->cache->delete($name);
	}
	
	public function flush()
	{
		Yii::$app->cache->flush();
	}

}