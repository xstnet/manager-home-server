<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
?>
	<meta charset="UTF-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<?= Html::cssFile('@static_frontend/frame/layui/css/layui.css')?>
	<?= Html::cssFile('@static_frontend/frame/static/css/style.css')?>
	<?= Html::cssFile('@static_frontend/css/style.css?v=' . Yii::$app->params['static_file_t'])?>
	<?= Html::cssFile('@static_frontend/frame/static/image/code.png', ['rel' => 'icon'])?>
