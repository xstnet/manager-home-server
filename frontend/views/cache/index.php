<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午3:51
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title>缓存管理</title>
		<?= $this->render('../public/header.php')?>
		<?php $this->head() ?>
		<style>
			#headImg {
				width: 120px;
				height: 120px;
			}
			.layui-disabled {
				background-color: #e4e4e4;
				color: #040404 !important;
			}
			body {
				background-color: #F2F2F2;
			}
		</style>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">

	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

	<div style="padding-left: 20px; margin-top: 20px">
		
		<div class="layui-btn-container">
			<button type="button" data-href="<?=Yii::$app->urlManager->createUrl('cache/clear-index')?>" class="layui-btn clear-cache">清除首页缓存</button>
			<button type="button" data-href="<?=Yii::$app->urlManager->createUrl('cache/clear-all')?>" class="layui-btn clear-cache">清除全部</button>
		</div>
	
	</div>




	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js')?>

	<script type="text/javascript">
		// layui方法
		layui.use(['layer',], function () {
			// 操作对象
			var form = layui.form
				, layer = layui.layer
				, $ = layui.jquery
				;
			
			// 清理缓存
			$('.clear-cache').click(function () {
				let url = $(this).data('href');
				$.getJSON(
					url,
					{},
					function (result) {
						layer.msg(result.message);
					}
				)
			})

		});
	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>