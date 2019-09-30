<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午9:20
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
use common\helpers\SystemSettingForm;

$name = '系统设置';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
		<?= $this->render('../public/header.php')?>
		<style>
			body {
				background-color: #F2F2F2;
			}
			.upload-img {
				max-width: 700px;
				min-width: 120px;
				height: 120px;
				border: 1px solid #ddd;
			}
			.setting-item { width: 70% !important; }
			.setting-code { width: 200px !important; }
			.layui-tab-content { padding: 20px }
		</style>
		<?php $this->head() ?>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">
	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>
	<div style="padding: 0 20px;">
		<form class="layui-form" id="setting-form" action="" >
		<div class="layui-tab layui-tab-card" lay-filter="setting-tab" style="background: #fff">
			<ul class="layui-tab-title">
				<?php $i = 0; foreach ($categories as $k => $item): $i++;?>
				<li class="<?=$i === 1 ? 'layui-this' : ''?>"><?=$item['name']?></li>
				<?php endforeach; $i = 0;?>
			</ul>

			<div class="layui-tab-content">
				<?php $i = 0; foreach ($systemConfigs as $categoryId => $configs): $i++;?>
				<div class="layui-tab-item <?=$i === 1 ? 'layui-show' : ''?>">
					<?php foreach ($configs as $item) :?>
						<div class="layui-form-item">
							<?php
								switch ($item['type']) {
									case 'text':
										echo SystemSettingForm::text($item);
										break;
									case 'imagefile' :
										echo SystemSettingForm::fileImage($item);
										break;
									case 'textarea' :
										echo SystemSettingForm::textarea($item);
										break;
									case 'radio' :
										echo SystemSettingForm::radio($item);
										break;
									case 'checkbox' :
										echo SystemSettingForm::checkbox($item);
										break;
								}
							?>
						</div>
					<?php endforeach;?>
				</div>
				<?php endforeach;?>
				<div class="layui-form-item">
					<div class="layui-input-block" style="padding: 20px 0">
						<button type="button" id="btn-add" class="layui-btn">添加</button>
						<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="form-submit">保存修改</button>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
	<div id="addDlg" class="dialog-wrap">
		<form style="padding-top: 30px" class="layui-form layui-form-pane" lay-filter="addForm" action="">
			<div class="layui-form-item">
				<label class="layui-form-label">类型</label>
				<div class="layui-input-block">
					<select name="type" lay-verify="required">
						<option value="text">文本框</option>
						<option value="textarea">文本域</option>
						<option value="radio">单选框</option>
						<option value="checkbox">复选框</option>
						<option value="imagefile">图片上传</option>
					</select>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">所属分类</label>
				<div class="layui-input-block">
					<select name="category_id" lay-verify="required">
						<?php foreach ($categories as $item): ?>
						<option value="<?=$item['id']?>"><?=$item['name']?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">名称</label>
				<div class="layui-input-inline">
					<input type="text" name="name" required  lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">值</label>
				<div class="layui-input-block">
					<input type="text" name="value" required  lay-verify="required" placeholder="请输入配置项值,多选框时使用,分隔" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">调用代码</label>
				<div class="layui-input-inline">
					<input type="text" name="code" required  lay-verify="required" placeholder="请输入code" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">排序</label>
				<div class="layui-input-inline">
					<input type="text" name="sort_value" required lay-verify="required|number" value="100" placeholder="请输入排序" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-form-item layui-form-text">
				<label class="layui-form-label">其他属性</label>
				<div class="layui-input-block">
					<textarea name="attribute" placeholder='通常类型为radio或checkbox时使用，json格式, 请使用双引号 [{"name": "开启", "value": 1}, {"name": "关闭", "value":0}]' class="layui-textarea"></textarea>
				</div>
				<div class='layui-form-mid layui-word-aux'></div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">描述</label>
				<div class="layui-input-block">
					<input type="text" name="description" required  placeholder="请输入描述" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button id="submitBtn" lay-submit="" lay-filter="form-add-submit"></button>
		</form>
	</div>

	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js')?>
	<script type="text/javascript">
		var layerIndex;
		// layui方法
		layui.use(['form', 'element', 'layer'], function () {
			var $ = layui.jquery,
				layer = layui.layer,
				element = layui.element, //Tab的切换功能，切换事件监听等，需要依赖element模块
				upload = layui.upload,
				form = layui.form;

			// 上传图片
			var uploadInst = uploadImage({
				elem: '.select-image-file',
				url: '<?= Yii::$app->urlManager->createUrl("upload/image-file")?>',
			});

			// 提交表单 保存设置
			form.on('submit(form-submit)', function (data) {
				// 不使用 layui 提供的data, 因为不会过滤 disabled 的input
				var formData = $('#setting-form').serializeJson();
				var url = '<?= Yii::$app->urlManager->createUrl("setting/save-setting")?>';
				$.post(
					url,
					formData,
					function (result) {
						layer.msg(result.message);
					},
					'json'
				)

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});

			// 添加设置项，显示对话框
			$('#btn-add').click(function () {
				showDialog('添加设置项', {
					content: $('#addDlg'), yes: function (index, layero) {
						layerIndex = index;
						// 模板表单提交，可以使用 layui form 进行监听
						$('#submitBtn').click();
					}
				});
			});

			/**
			 * 添加设置项，保存
			 */
			form.on('submit(form-add-submit)', function (data) {
				var url = '<?= Yii::$app->urlManager->createUrl("setting/add-setting")?>';
				$.post(
					url,
					data.field,
					function (result) {
						layer.msg(result.message, function () {
							if (result.code === AJAX_STATUS_SUCCESS) {
								location.reload();
							}
						});
					},
					'json'
				)

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});
			/**
			 * 切换分类时，禁用其他分类的设置
			 */
			element.on('tab(setting-tab)', function (data) {
				disabledInput(data.index);
			});
			
			function disabledInput(index) {
				$('.layui-tab-content :input').prop('disabled', true);
				$('.layui-tab-content :checkbox').prop('disabled', true);
				$('.layui-tab-content :radio').prop('disabled', true);

				$('.layui-tab-item').eq(index).find(':input').prop('disabled', false);
				$('.layui-tab-item').eq(index).find(':checkbox').prop('disabled', false);
				$('.layui-tab-item').eq(index).find(':radio').prop('disabled', false);
				$('button').prop('disabled', false);
			}

			// 默认禁用其他分类的设置，显示第一个
			disabledInput(0);
		});
	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>