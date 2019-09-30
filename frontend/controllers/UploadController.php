<?php
/**
 * Desc: 用户管理
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午3:56
 */

namespace frontend\controllers;

use common\models\form\UploadForm;
use yii\filters\VerbFilter;
use Yii;
use yii\web\UploadedFile;

class UploadController extends AdminLogController
{
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'image-file' => ['post'],
						'image-data' => ['post'],
					],
				],
			]
		);
	}


	/**
	 * @Desc: 通过文件形式上传图片
	 * @return array
	 */
	public function actionImageFile()
	{
		$model = new UploadForm();
		if (Yii::$app->request->isPost) {
			$model->imageFile = UploadedFile::getInstance($model, 'file');
			$model->scenario =  UploadForm::SCENARIO_IMAGE_FILE;
			$result = $model->uploadImageFile();
			if ($result) {
				// 文件上传成功
				return self::ajaxSuccess('上传成功', ['file' => $result]);
			} else {
				$error = $model->getErrors()['imageFile'][0];
				return self::ajaxReturn($error);
			}
		}
	}

	public function actionImageData()
	{
		$model = new UploadForm();

		if (Yii::$app->request->isPost) {
			$model->imageData = Yii::$app->request->getBodyParam('file');
			$model->scenario =  UploadForm::SCENARIO_IMAGE_DATA;
			$result = $model->uploadImageData();
			if ($result) {
				// 文件上传成功
				return self::ajaxSuccess(self::AJAX_MESSAGE_SUCCESS, ['file' => $result]);
			} else {
				$error = $model->getErrors()['imageData'][0];
				return self::ajaxReturn($error);
			}
		}
	}

}