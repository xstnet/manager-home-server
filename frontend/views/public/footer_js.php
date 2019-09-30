<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
?>
<?= Html::jsFile('@static_frontend/frame/layui/layui.js')?>
<?= Html::jsFile('@static_frontend/js/common.js?v=' . Yii::$app->params['static_file_t'])?>