<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-22
 * Time: 下午3:51
 */

/* @var $this yii\web\View */
/* @var $model \common\models\Article */

use yii\helpers\Html;
use yii\helpers\Json;

$name = '编辑文章';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?></title>
		<?= $this->render('../public/header.php')?>
		<?= Html::jsFile('@static_frontend/plugins/ueditor/ueditor.config.js')?>
		<?= Html::jsFile('@static_frontend/plugins/ueditor/ueditor.all.min.js')?>
		<?= Html::jsFile('@static_frontend/plugins/ueditor/lang/zh-cn/zh-cn.js')?>
		<?php $this->head() ?>
		<style>
			.upload-img {
				max-width: 700px;
				min-width: 120px;
				height: 120px;
				border: 1px solid #ddd;
			}
			body {
				background-color: #F2F2F2;
			}
			#titleStyle {margin-bottom: 0 !important;}
			.title-style-btn {
				border: 1px solid #e6e6e6;
				padding: 0px 14px !important;
			}
			#content { min-height: 500px};
		</style>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">

	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

	<div style="padding: 0 20px;">
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md12">
				<div class="layui-bg-green" style="padding: 5px 15px;">
					正在使用富文本编辑器
					<button id="useMarkdownEdit" type="button" class="layui-btn layui-btn-primary">使用Markdown编辑器</button>
				</div>
				<div class="layui-card">
					<div class="layui-card-header">
						<fieldset class="layui-elem-field layui-field-title">
							<legend>编辑文章</legend>
						</fieldset>
					</div>
					<div class="layui-card-body">
						<form class="layui-form" action="">
							<input type="hidden" name="id" value="<?=$model->id?>">
							<div class="layui-form-item">
								<label class="layui-form-label">标题</label>
								<div class="layui-input-block">
									<input type="text" name="title" lay-verify="required" placeholder="请输入标题" style="<?=$model->title_style?>" autocomplete="on" value="<?=Html::encode($model->title)?>" id="inputTitle" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">标题样式</label>
								<div class="layui-input-block">
									<input type="hidden" name="title_style">
									<div id="titleStyle"></div>
									<div data-style="fontWeight" data-style-value="bold" class="title-style-btn layui-btn layui-btn-primary">B</div>
									<div data-style="fontStyle" data-style-value="italic" class="title-style-btn layui-btn layui-btn-primary"><i><b>I</b></i></div>
									<div data-style="fontSize" data-style-value="14px" class="title-style-btn style-font-size layui-btn layui-btn-primary">14</div>
									<div data-style="fontSize" data-style-value="16px" class="title-style-btn style-font-size layui-btn layui-btn-primary">16</div>
									<div data-style="fontSize" data-style-value="18px" class="title-style-btn style-font-size layui-btn layui-btn-primary">18</div>
									<div data-style="fontSize" data-style-value="20px" class="title-style-btn style-font-size layui-btn layui-btn-primary">20</div>
									<div data-style="textDecoration" data-style-value="underline" class="title-style-btn layui-btn layui-btn-primary"><u>U</u></div>
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">标题图片</label>
								<div class="layui-input-inline">
									<div class="layui-upload">
										<div class="layui-upload-list">
											<img class="upload-img" src="<?=Html::encode($model->title_image)?>">
											<p class="upload-message"></p>
											<input type="hidden" name="title_image" value="<?=Html::encode($model->title_image)?>">
										</div>
										<button type="button" class="layui-btn layui-btn-warm layui-btn-sm" id="uploadImg">选择图片</button>
									</div>
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">作者</label>
								<div class="layui-input-inline">
									<input type="text" name="author" lay-verify="required" value="<?=Html::encode($model->author)?>" autocomplete="on" placeholder="请输入作者" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">来源</label>
								<div class="layui-input-inline">
									<input type="text" name="source" lay-verify="required" value="<?=Html::encode($model->source)?>" autocomplete="on" placeholder="请输入来源" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">所属分类</label>
								<div class="layui-input-block">
									<select name="category_id" id="categoryId" lay-verify="required">
										<?= $treeSelect?>
									</select>
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">关键字</label>
								<div class="layui-input-block">
									<input type="text" name="keyword" autocomplete="on"  placeholder="请输入关键字" value="<?=Html::encode($model->keyword)?>" class="layui-input">
									<div class="layui-form-mid layui-word-aux">多个以英文逗号, 隔开</div>
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">是否显示</label>
								<div class="layui-input-block">
									<?=Html::input('radio', 'is_show', 1, ['checked' => ($model->is_show == 1), 'title' => '显示'])?>
									<?=Html::input('radio', 'is_show', 0, ['checked' => ($model->is_show == 0), 'title' => '不显示'])?>
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">允许评论</label>
								<div class="layui-input-block">
									<?=Html::input('radio', 'is_allow_comment', 1, ['checked' => ($model->is_show == 1), 'title' => '允许'])?>
									<?=Html::input('radio', 'is_allow_comment', 0, ['checked' => ($model->is_show == 0), 'title' => '不允许'])?>
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">排序值</label>
								<div class="layui-input-inline">
									<input type="text" name="sort_value" lay-verify="required|number" value="<?=Html::encode($model->sort_value)?>" autocomplete="on" placeholder="请输入排序值" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item layui-form-text">
								<label class="layui-form-label">内容描述</label>
								<div class="layui-input-block">
									<textarea name="description" placeholder="请输入内容,最大长度为200字，默认截取文章正文前200字" class="layui-textarea"><?=$model->description?></textarea>
								</div>
							</div>
							<div class="layui-form-item layui-form-text">
								<label class="layui-form-label">文章内容</label>
								<div class="layui-input-block">
									<script id="content" name="content" type="text/plain"><?=Html::decode($model->content->content)?></script>
								</div>
							</div>

							<div class="layui-form-item">
								<div class="layui-input-block" style="padding: 20px 0">
									<button style="font-size: 16px" class="layui-btn" lay-submit="" lay-filter="form-submit"><i class="layui-icon layui-icon-release"></i> 保存</button>
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
		var titleStyleStr = "<?=$model->title_style?>";
		// layui方法
		layui.use(['form', 'layer', 'upload', 'colorpicker', 'vip_tab'], function () {
			// 操作对象
			var form = layui.form
				, layer = layui.layer
				, $ = layui.jquery
				, colorpicker = layui.colorpicker
				, upload = layui.upload
				, vipTab = layui.vip_tab,
				titleStyle = {color: ''};

			// 默认选中该文章的分类
			$('#categoryId').val(<?=$model->category_id?>);
			form.render('select');

			// 设置标题样式
			if (titleStyleStr != '') {
				// 去除最后一个 分号
				titleStyleStr = titleStyleStr.substr(0, titleStyleStr.length - 1);
				titleStyleStr = titleStyleStr.replace(/\-/g, '');
				var titleStyleArray = titleStyleStr.split(';');
				$('.title-style-btn').each(function (index, item) {
					for (var i=0; i<titleStyleArray.length; i++) {
						var styleKey = $(this).data('style').toLowerCase();
						var style = titleStyleArray[i].split(':');
						if (style[0] === 'color') {
							titleStyle.color = style[1];
							continue;
						}
						if (styleKey === style[0]) {
							if (styleKey === 'fontsize') {
								if ($(this).data('style-value') != style[1]) {
									continue;
								}
							}
							titleStyle[ $(this).data('style') ] = $(this).data('style-value');
							$(this).removeClass('layui-btn-primary');
						}
					}
				});
			}

			// 设置颜色
			colorpicker.render({
				elem: '#titleStyle',
				color: titleStyle.color,
				predefine: true, // 开启预定义颜色
				change: function (color) {
					titleStyle.color = color;
					if (color == '') {
						delete titleStyle.color;
					}
					changeTitleStyle();
				},
				done: function(color){
					this.change(color)
				}
			});

			// 更新标题样式
			$('.title-style-btn').click(function () {
				$(this).toggleClass('layui-btn-primary');
				if ($(this).hasClass('layui-btn-primary')) {
					delete titleStyle[ $(this).data('style') ];
				} else {
					titleStyle[ $(this).data('style') ] = $(this).data('style-value');
				}
				// 设置字号时的改变
				if ($(this).hasClass('style-font-size')) {
					$('.style-font-size').addClass('layui-btn-primary');
					$(this).removeClass('layui-btn-primary');
					// 取消选择了，则去除样式
					if (!titleStyle[ $(this).data('style') ]) {
						$(this).addClass('layui-btn-primary');
					}
				}
				changeTitleStyle();
			});

			/**
			 * 更新 title 样式
			 */
			function changeTitleStyle() {
				$('#inputTitle').removeAttr('style')
				$('#inputTitle').css(titleStyle);
			}
			//标题图片上传
			var uploadInst = uploadImage({
				elem: '#uploadImg',
				url: '<?= Yii::$app->urlManager->createUrl("upload/image-file")?>',
			});

			// 保存表单
			form.on('submit(form-submit)', function (data) {
				var formData = data.field;
				if (titleStyle.color == '') {
					delete titleStyle.color;
				}
				formData.title_style = titleStyle;
				console.log(formData);
				var url = '<?= Yii::$app->urlManager->createUrl("article/save-article")?>';
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

			$('#useMarkdownEdit').click(function () {

				var tabId = vipTab.getThisTabId();
				vipTab.add('', '编辑文章Markdown', '<?=Yii::$app->urlManager->createUrl(["article/edit", 'id'=>$model->id, "type"=>"markdown"])?>');
				vipTab.del(tabId);
			})

		});

		var ue = UE.getEditor('content');
		
	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>