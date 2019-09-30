<?php
/**
 * Task
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-26
 * Time: 上午10:00
 */
namespace console\controllers;

use common\models\Config;
use common\models\CountIp;
use common\models\CountRecord;
use common\models\CountTotal;
use common\models\TaskMail;
use Yii;
use common\models\Article;
use common\models\ArticleTag;

class TaskController extends BaseController
{
	/**
	 * 发送邮件, 手动执行 run `php ./yii task/send-mails`
	 *
	 * 自动执行
	 * ```
	 * 每天9点-23点, 每隔半小时执行一次
	 * 0,30 9-23 * * * cd project && /usr/bin/php ./yii task/send-mails
	 * ```
	 * @return bool
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionSendMails()
	{
		$this->printf('开始发送邮件');
		
		$mailList = TaskMail::find()
			->where(['is_send' => TaskMail::IS_SEND_FALSE])
			->andWhere(['<', 'call_count', TaskMail::MAX_CALL_COUNT])
			->orderBy(['id' => SORT_ASC])
			->limit(10)
			->all();
		
		$this->printf(sprintf('本次共有%d封邮件待发送', count($mailList)));
		
		if (empty($mailList)) {
			$this->printf("结束发送邮件" . PHP_EOL);
			return true;
		}
		
		/**
		 * 更新邮件服务
		 */
		// 后台设置的邮件配置
		$mailConfig = Config::find()
			->where(['category_id' => 2])
			->asArray()
			->indexBy('code')
			->all();
		
		// 代码中配置的参数
		$mailParams = Yii::$app->getComponents()['mailer'];
		
		$newMailerConfig = [
			'transport' => [
				'host' => $mailConfig['email_host']['value'],
				// qq邮箱
				'username' => $mailConfig['email_username']['value'],
				//授权码, 什么是授权码， http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
				'password' => $mailConfig['email_password']['value'],
				'port' => $mailConfig['email_port']['value'],
				'encryption' => $mailConfig['email_encryption']['value'],
			],
			'messageConfig' => [
				'from' => ["{$mailConfig['email_from']['value']}" => $mailConfig['email_from_title']['value'],],
			],
		];
		
		$newMailerConfig = \yii\helpers\ArrayHelper::merge($mailParams, $newMailerConfig);
		
		Yii::$app->set('mailer', $newMailerConfig);
		
		$mail = Yii::$app->mailer->compose();
		
		$successCount = 0;
		foreach ($mailList as $item) {
			try {
				$result = $mail->setTo($item->to_mail)
					->setSubject($item->subject)
					->setHtmlBody($item->content)
					->send();
				if ($result) {
					$item->send_at = time();
					$item->is_send = TaskMail::IS_SEND_TRUE;
					$successCount ++;
				}
				$item->call_count ++;
				// 不判断, 失败了就等下次计划任务
				$item->save();
			} catch (\Exception $e) {
				$this->printf(sprintf('任务ID: %d 发送失败, 原因%s', $item->id, $e->getMessage()));
				Yii::error($e->getTraceAsString());
			}
		}
		
		$this->printf(sprintf('共成功发送%d封邮件', $successCount));
		$this->printf("结束发送邮件" . PHP_EOL);
	}
	
	public function actionDayCount()
	{
		$this->printf('开始每日统计');
		try {
			Yii::$app->redis->select(Yii::$app->params['redis_database']['keep_cache']);
			
			$this->processDayCount(strtotime(date('Y-m-d', strtotime('yesterday'))));
			$this->processDayCount(strtotime(date('Y-m-d', strtotime('today'))));
			
			Yii::$app->redis->select(Yii::$app->params['redis_database']['default']);
		} catch (\Exception $e) {
			$this->printf('每日统计出错, 原因:' . $e->getMessage());
			$this->printf('Trace:' . $e->getTraceAsString());
		}
		
		$this->printf('结束每日统计');
	}
	
	public function processDayCount($dateAt)
	{
		/**
		 * @var $redis \yii\redis\Connection
		 */
		$redis = Yii::$app->redis;
		
		$transaction = Yii::$app->db->beginTransaction();
		
		try {
			$countDayKey = date('Y-m-d', $dateAt) . '_day_count';
			$countIpKey = date('Y-m-d', $dateAt) . '_ip_count';
			$countTotalKey = date('Y-m-d', $dateAt) . '_total_count';
			
			$ipModel = CountIp::findOne(['date_at' => $dateAt]);
			if (empty($ipModel)) {
				$ipModel = new CountIp();
				$ipModel->date_at = $dateAt;
				$ipModel->count = 0;
				$ipModel->saveModel($transaction);
			}
			
			$dayModel = CountRecord::findOne(['date_at' => $dateAt]);
			if (empty($dayModel)) {
				$dayModel = new CountRecord();
				$dayModel->date_at = $dateAt;
				$dayModel->count = 0;
				$dayModel->saveModel($transaction);
			}
			
			$totalModel = CountTotal::findOne(['date_at' => $dateAt]);
			if (empty($totalModel)) {
				$totalModel = new CountTotal();
				$totalModel->date_at = $dateAt;
				$totalModel->total_count = (int) CountTotal::find()->max('total_count');
				$totalModel->saveModel($transaction);
			}
			
			/////////////////////////////////////////////////////////////
			
			// 每日Ip统计
			$ipCount = $redis->hlen($countIpKey);
			if ($ipCount > 0) {
				$ipModel->count = $ipCount;
				$ipModel->saveModel($transaction);
			}
			
			// 每日统计
			$dayCount = $redis->get($countDayKey);
			if ($dayCount > 0) {
				$dayModel->count += $dayCount;
				$dayModel->saveModel($transaction);
			}
			
			// 总统计
			$totalCount = $redis->get($countTotalKey);
			if ($totalCount > 0) {
				$totalModel->total_count += $ipCount;
				$totalModel->saveModel($transaction);
			}
			
			if ($dateAt < strtotime(date('Y-m-d', strtotime('today')))) {
				$redis->del($countDayKey);
				$redis->del($countIpKey);
				$redis->del($countTotalKey);
			} else {
				$redis->decrby($countTotalKey, (int) $totalCount);
				$redis->decrby($countDayKey, (int) $dayCount);
			}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}