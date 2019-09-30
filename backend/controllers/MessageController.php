<?php
/**
 * Message
 * Created by PhpStorm.
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 19-7-25
 * Time: 下午3:37
 */

namespace backend\controllers;


use common\models\Article;
use common\models\Messages;
use common\models\TaskMail;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;

class MessageController extends BaseController
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'release' => ['post'],
					'index' => ['get'],
				],
			],
			'PageCache' => [
				'class' => 'yii\filters\PageCache',
				'only' => ['index'],
				'duration' => 0,
				'enabled' => true,
				'variations' => [
					Yii::$app->request->get('page', 1),
					Yii::$app->request->get('debug', 1),
				],
				'dependency' => [
					'class' => 'yii\caching\DbDependency',
					'sql' => "SELECT COUNT(*) FROM x_messages",
				],
			],
		];
	}
	
	/**
	 * Displays Message Content.
	 * @return string
	 */
	public function actionIndex()
	{
		$query = Messages::find()->orderBy('id desc');
		
		list ($count, $pages) = $this->getPage($query, 30);
		
		$messageList = $query->asArray()
			->all();
		
		return $this->render('index', [
			'messageList' => $messageList,
			'pages' => $pages,
		]);
		
	}
	
	/**
	 * 发布留言
	 */
	public function actionRelease()
	{
		// 验证留言间隔, 1分钟内只能发一次
		$session = Yii::$app->session;
		$session->open();
		$lastPubAt = $session->get('message_last_pub_at', 0);
		
		if ($lastPubAt > 0 && ($lastPubAt + 60) > time()) {
//			return "<script>alert('1分钟内只能发布一次哦!');history.go(-1)</script>";
		}
		$params = Yii::$app->request->post();
		$message = new Messages();
		$message->avatar = sprintf('/uploads/avatar/%d.jpg', rand(1, 5));
		$message->nickname = HtmlPurifier::process(trim($params['nickname']));
		$message->email = HtmlPurifier::process(trim($params['email']));
		$message->content = trim($params['content']);
		$message->ip = Yii::$app->request->userIP;
		
		$error = '';
		if (!$message->save()) {
			$error = current($message->getFirstErrors());
		}
		
		if ($error != '') {
			return "<script>alert('$error');history.go(-1)</script>";
		}
		
		$session->set('message_last_pub_at', time());
		
		$content = '<p>留言人: ' . $message->nickname . '</p>';
		$content .= '<p> 邮箱: ' . $message->email . '</p>';
		$content .= '<div>内容: ' . \yii\helpers\Html::encode($message->content) . '</div>';
		
		// 创建邮件任务
		$mailTask = new TaskMail();
		$mailTask->addOne([
			'to_mail' => 'shantongxu@qq.com',
			'subject' => '博客有新的留言, 请即时查看',
			'content' => $content,
		]);
		
		$url = $params['from'] == 'message' ? '/message.html' : '/about.html';
		return "<script>alert('发布成功');location.href='{$url}'</script>";
		
	}
	
	public function afterAction($action, $result)
	{
		$result = $this->renderContentFilter($result);
		return parent::afterAction($action, $result);
	}
}