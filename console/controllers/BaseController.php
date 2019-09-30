<?php
/**
 *
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-26
 * Time: 上午10:00
 */
namespace console\controllers;

use yii\console\Controller;
use Yii;

class BaseController extends Controller
{
	
	public function printf($message, $writeLog = true)
	{
		if ($writeLog) {
			Yii::warning($message);
		}
		
		if (is_array($message)) {
			$message = json_encode($message, JSON_UNESCAPED_UNICODE);
		}
		
		echo $message . PHP_EOL;
	}
}