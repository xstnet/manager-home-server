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

$name = '发布文章';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?></title>
		<?= $this->render('../public/header.php')?>
		<link rel="stylesheet" href="/static/plugins/mditor/dist/css/mditor.min.css" />
		<script src="/static/plugins/mditor/dist/js/mditor.min.js"></script>
		
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
					正在使用Markdown编辑器
					<button id="useHtmlEdit" type="button" class="layui-btn layui-btn-primary">使用富文本编辑器</button>
				</div>
				
				<div class="layui-card">
					<div class="layui-card-header">
						<fieldset class="layui-elem-field layui-field-title">
							<legend>发布新文章</legend>
						</fieldset>
					</div>
					<div class="layui-card-body">
						<form class="layui-form" action="">
							<div class="layui-form-item">
								<label class="layui-form-label">标题</label>
								<div class="layui-input-block">
									<input type="text" name="title" lay-verify="required" placeholder="请输入标题" autocomplete="on" value="" id="inputTitle" class="layui-input">
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
											<img class="upload-img" src="">
											<p class="upload-message"></p>
											<input type="hidden" id="titleImage" name="title_image" value="">
										</div>
										<button type="button" class="layui-btn layui-btn-warm layui-btn-sm" id="uploadImg">选择图片</button>
									</div>
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">作者</label>
								<div class="layui-input-inline">
									<input type="text" name="author" lay-verify="required" value="<?=Yii::$app->user->identity->nickname?>" autocomplete="on" placeholder="请输入作者" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">来源</label>
								<div class="layui-input-inline">
									<input type="text" name="source" lay-verify="required" value="原创" autocomplete="on" placeholder="请输入来源" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">所属分类</label>
								<div class="layui-input-block">
									<select name="category_id" lay-verify="required">
										<?= $treeSelect?>
									</select>
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">关键字</label>
								<div class="layui-input-block">
									<input type="text" name="keyword" autocomplete="on"  placeholder="请输入关键字" value="" class="layui-input">
									<div class="layui-form-mid layui-word-aux">多个以英文逗号, 隔开</div>
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">是否显示</label>
								<div class="layui-input-block">
									<input type="radio" name="is_show" value="1" checked title="显示">
									<input type="radio" name="is_show" value="0" title="不显示" >
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">允许评论</label>
								<div class="layui-input-block">
									<input type="radio" name="is_allow_comment" value="1" checked title="允许">
									<input type="radio" name="is_allow_comment" value="0" title="不允许" >
								</div>
							</div>
							<div class="layui-form-item" pane>
								<label class="layui-form-label">排序值</label>
								<div class="layui-input-inline">
									<input type="text" name="sort_value" lay-verify="required|number" value="100" autocomplete="on" placeholder="请输入排序值" class="layui-input">
								</div>
							</div>
							<div class="layui-form-item layui-form-text">
								<label class="layui-form-label">内容描述</label>
								<div class="layui-input-block">
									<textarea name="description" placeholder="请输入内容,最大长度为200字，默认截取文章正文前200字" class="layui-textarea"></textarea>
								</div>
							</div>
							<div class="layui-form-item layui-form-text">
								<label class="layui-form-label">文章内容</label>
								<div class="layui-input-block">
									<textarea name="editor" id="editor"></textarea>
<!--									<div id="content" name="content" type="text/plain"></div>-->
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">发布时间</label>
								<div class="layui-input-block">
									<input style="width: 300px" type="text" name="release_time" autocomplete="off"  value="" class="layui-input my-input-datetime">
									<div class="layui-form-mid layui-word-aux"></div>
								</div>
							</div>
							<div class="layui-form-item">
								<div class="layui-input-block" style="padding: 20px 0">
									<button style="font-size: 16px" class="layui-btn" lay-submit="" lay-filter="form-submit"><i class="layui-icon layui-icon-release"></i> 发布</button>
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

		var mditor =  Mditor.fromTextarea(document.getElementById('editor'));


		//获取或设置编辑器的值
		mditor.on('ready',function(){
			mditor.editor.on('paste',function(event){
				parseImage(event);
			});
			// auto complete Special symbols
			mditor.editor.on('input',function(event){
				return false;
				var specialSymbols = {
					"'": "'",
					'"': '"',
					'[': ']',
					'<': '>',
					'{': '}',
					'(': ')',
				};

				// console.log(specialSymbols);
				var beforeOneChar = mditor.editor.getBeforeText(1);
				var afterOneChar = mditor.editor.getBeforeText(-1);
				console.log(afterOneChar);
				if (specialSymbols.hasOwnProperty(beforeOneChar)) {
					if (specialSymbols[beforeOneChar] != afterOneChar) {
						mditor.editor.insertAfterText(specialSymbols[beforeOneChar]);

					}
				}
				
			});

		});
		
		// layui方法
		layui.use(['form', 'layer', 'upload', 'vip_tab', 'colorpicker'], function () {
			// 操作对象
			var form = layui.form
				, layer = layui.layer
				, vipTab = layui.vip_tab
				, $ = layui.jquery
				, colorpicker = layui.colorpicker
				, upload = layui.upload,
				titleStyle = {};

			colorpicker.render({
				elem: '#titleStyle',
//				color: '#c71585',
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

			// 提交表单 发布
			form.on('submit(form-submit)', function (data) {
				var formData = data.field;
				formData.title_style = titleStyle;
				formData.content = mditor.viewer.html;
				formData.markdown_content = mditor.value;
				console.log(formData);
				var url = '<?= Yii::$app->urlManager->createUrl("article/add-article")?>';
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
			
			$('#useHtmlEdit').click(function () {
				var tabId = vipTab.getThisTabId();
				vipTab.add('', '发布文章Html', '<?=Yii::$app->urlManager->createUrl(["article/add", "type"=>"html"])?>');
				vipTab.del(tabId);
			})

		});


		//document.addEventListener('paste', function (event) {
		function parseImage (event) {
			var clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
			console.log(event);
			var isChrome = false;
			if ( event.clipboardData || event.originalEvent ) {
				//not for ie11  某些chrome版本使用的是event.originalEvent
				var clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
				if (clipboardData.items) {
					// for chrome
					var items = clipboardData.items,
						len = items.length,
						blob = null;
					isChrome = true;

					//items.length比较有意思，初步判断是根据mime类型来的，即有几种mime类型，长度就是几（待验证）
					//如果粘贴纯文本，那么len=1，如果粘贴网页图片，len=2, items[0].type = 'text/plain', items[1].type = 'image/*'
					//如果使用截图工具粘贴图片，len=1, items[0].type = 'image/png'
					//如果粘贴纯文本+HTML，len=2, items[0].type = 'text/plain', items[1].type = 'text/html'
					// console.log('len:' + len);
					// console.log(items[0]);
					// console.log(items[1]);
					// console.log( 'items[0] kind:', items[0].kind );
					// console.log( 'items[0] MIME type:', items[0].type );
					// console.log( 'items[1] kind:', items[1].kind );
					// console.log( 'items[1] MIME type:', items[1].type );

					//阻止默认行为即不让剪贴板内容在div中显示出来
					// event.preventDefault();

					//在items里找粘贴的image,据上面分析,需要循环
					for (var i = 0; i < len; i++) {
						if (items[i].type.indexOf("image") !== -1) {
							//getAsFile() 此方法只是living standard firefox ie11 并不支持
							blob = items[i].getAsFile();
							uploadImgFromPaste(blob, 'paste', isChrome);
						}
					}
				}
			}
		}
		
		//调用图片上传接口,将file文件以formData形式上传
		function uploadImgFromPaste (file, type, isChrome) {
			var formData = new FormData();
			formData.append('file', file);
			formData.append('submission-type', type);
			formData.append('_csrf_token_frontend_xstnet', '<?=Yii::$app->request->csrfToken?>');

			var xhr = new XMLHttpRequest();
			xhr.open('POST', '/index.php?r=upload/image-file');
			xhr.onload = function () {
				// console.log(xhr.readyState);
				if ( xhr.readyState === 4 ) {
					if ( xhr.status === 200 ) {
						var data = JSON.parse(xhr.responseText);
						var imageMd = "![alt]("+ (data && data.data.file) +")";
						mditor.editor.insertBeforeText(imageMd);
						// mditor.editor.wrapSelectText('before', 'after');
					} else {
						console.log( xhr.statusText );
					}
				}
			}
			xhr.onerror = function (e) {
				console.log( xhr.statusText );
			}
			xhr.send(formData);
		}

	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>