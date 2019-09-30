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
use yii\helpers\Json;

$name = '个人信息';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
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

	<div style="padding-left: 20px;">
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md5">
				<div class="layui-card">
					<div class="layui-card-header">
						<fieldset class="layui-elem-field layui-field-title">
							<legend>修改个人信息</legend>
						</fieldset>
					</div>
					<div class="layui-card-body">
						<form class="layui-form" action="">
							<div class="layui-form-item">
								<label class="layui-form-label">用户名</label>
								<div class="layui-input-inline">
									<input type="text" name="username" disabled autocomplete="off" value="<?=Yii::$app->user->identity->username?>" class="layui-input layui-disabled">
								</div>
								<div class="layui-form-mid layui-word-aux">用户名不能修改</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">昵称</label>
								<div class="layui-input-inline">
									<input type="text" name="nickname" lay-verify="required" value="<?=Yii::$app->user->identity->nickname?>" autocomplete="off" placeholder="请输入昵称" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">密码</label>
								<div class="layui-input-inline">
									<input type="text" name="password" autocomplete="off" placeholder="请输入密码" class="layui-input">
								</div>
								<div class="layui-form-mid layui-word-aux">不修改密码请留空</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">邮箱</label>
								<div class="layui-input-inline">
									<input type="text" name="email" value="<?=Yii::$app->user->identity->email?>" autocomplete="off" placeholder="请输入邮箱" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">头像</label>
								<div class="layui-input-inline">
									<div class="layui-upload">
										<div class="layui-upload-list">
											<img class="upload-img" src="<?=Yii::$app->user->identity->avatar ? : '/'. Yii::$app->params['defaultAvatar']?>" id="headImg">
											<p class="upload-message"></p>
											<input type="hidden" id="headImgInput" name="avatar" value="<?=Yii::$app->user->identity->avatar ? : Yii::$app->params['defaultAvatar']?>">
										</div>
										<button type="button" class="layui-btn layui-btn-warm layui-btn-sm" id="uploadHeadImg">选择图片</button>
									</div>
								</div>
							</div>

							<div class="layui-form-item">
								<div class="layui-input-block" style="padding: 20px 0">
									<button class="layui-btn" lay-submit="" lay-filter="form-submit">保存修改</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>



	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js')?>

	<script type="text/javascript">
		// layui方法
		layui.use(['form', 'layer', 'upload',], function () {
			// 操作对象
			var form = layui.form
				, layer = layui.layer
				, $ = layui.jquery
				, upload = layui.upload;

			//头像上传
			var uploadInst = uploadImage({
				elem: '#uploadHeadImg',
				url: '<?= Yii::$app->urlManager->createUrl("upload/image-file")?>',
			});

			// 提交表单
			form.on('submit(form-submit)', function (data) {
				console.log(data.field);
				var url = '<?= Yii::$app->urlManager->createUrl("user/save-user-profile")?>';
				if (data.field.password.length < 5 && data.field.password.length > 0) {
					layer.msg('密码长度不能小于5位');
					return false;
				}

				if (data.field.nickname.length < 2) {
					layer.msg('昵称长度不能小于2位');
					return false;
				}
				if (data.field.nickname.length > 30) {
					layer.msg('昵称长度不能大于30位');
					return false;
				}
				$.post(
					url,
					data.field,
					function (result) {
						layer.msg(result.message);
					},
					'json'
				)

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});

		});



	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>