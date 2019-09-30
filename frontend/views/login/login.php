<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<title>登录</title>
	<?= $this->render('../public/header.php')?>

	<?php $this->head() ?>
</head>
<body style="background-color: #e6e6e6">

<div class="login-main">
	<header class="layui-elip">后台登录</header>
	<form class="layui-form">
		<input type="hidden" name="_csrf_token_frontend_xstnet" value="<?=Yii::$app->request->csrfToken?>" />
		<div class="layui-input-inline">
			<input type="text" name="username" required lay-verify="required" placeholder="账号" class="layui-input">
		</div>
		<div class="layui-input-inline">
			<input type="password" name="password" required lay-verify="required" placeholder="密码" autocomplete="off" class="layui-input">
		</div>
		<div class="layui-input-inline login-btn" >
			<button lay-submit="" lay-filter="login" class="layui-btn">登录</button>
		</div>
		<hr/>
<!--		<div class="layui-input-inline">
			<button type="button" class="layui-btn layui-btn-primary">QQ登录</button>
		</div>
		<div class="layui-input-inline">
			<button type="button" class="layui-btn layui-btn-normal">微信登录</button>
		</div>-->
<!--		<p><a href="javascript:;" class="fl">立即注册</a><a href="javascript:;" class="fr">忘记密码？</a></p>-->
	</form>
</div>


<?= $this->render('../public/footer_js.php')?>
<script type="text/javascript">

	// alert($);


	layui.use(['form'], function () {
		// 操作对象
		var form = layui.form;
		var $ = layui.jquery;

		form.on('submit(login)', function (data) {
			$.post(
				'<?= Yii::$app->urlManager->createUrl(['login/login'])?>',
				data.field,
				function (result) {
					layer.msg(result.message, {time: 1500});
					if (result.code == AJAX_STATUS_SUCCESS) {
						setTimeout(function () {
							window.location.href='<?= Yii::$app->urlManager->createUrl(['site/index'])?>';
						}, 500);
					}
				},
				'json'
			);

			return false;
		});
	});

</script>
</body>
</html>
