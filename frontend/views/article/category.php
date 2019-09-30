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
use yii\helpers\Json;

$name = '分类';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
		<?= $this->render('../public/header.php')?>
		<?= Html::cssFile('@static_frontend/css/eleTree.css')?>
		<?php $this->head() ?>
		<style>
			.layui-3-btn .layui-layer-btn .layui-layer-btn0 {
				border-color: #FF5722;
				background-color: #FF5722;
				color: #fff;
			}
			.layui-3-btn .layui-layer-btn .layui-layer-btn1 {
				border-color: #1E9FFF;
				background-color: #1E9FFF;
				color: #fff;
			}
			#moveArticleToCategory { margin-top: 30px }
			.dialog-wrap { min-height: 50px }
		</style>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">
	<!-- 工具集 -->
	<div class="my-btn-box">
    <span class="fl">
        <a class="layui-btn layui-btn-danger radius btn-delect" id="btn-delete">删除</a>
		<!--        <a class="layui-btn btn-add btn-default" id="btn-add">添加--><?//= $name?><!--</a>-->
    </span>
		<span class="fr">
         <a class="layui-btn btn-add btn-default" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></a>
    </span>
	</div>
	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

	<div style="padding: 20px; background-color: #F2F2F2; min-height: 100vh">
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md3">
				<div class="layui-card">
					<div class="layui-card-header">分类列表</div>
					<div class="layui-card-body">
						<!--分类树-->
						<div class="eleTree" id="treeList" lay-filter="data1"></div>
					</div>
				</div>
			</div>
			<div class="layui-col-md5">
				<div class="layui-card">
					<div class="layui-card-header">分类信息</div>
					<div class="layui-card-body">
						<div style="padding-bottom: 20px;">
							<form style="padding-top: 30px" class="layui-form" lay-filter="actionForm" action="">
								<div class="layui-form-item">
									<label class="layui-form-label">所属分类</label>
									<div class="layui-input-block">
										<select name="parent_id" lay-verify="required">
											<option value="0">一级分类</option>
											<?= $treeSelect?>
										</select>
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">名称</label>
									<div class="layui-input-block">
										<input type="text" name="category_name" required  lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">排序值</label>
									<div class="layui-input-inline">
										<input type="text" name="sort_value" value="30"  lay-verify="required|number" placeholder="请输入排序值" autocomplete="off" class="layui-input">
										<div class="layui-form-mid layui-word-aux">按照从小到大的顺序排列</div>
									</div>
								</div>
								<div class="layui-form-item" pane>
									<label class="layui-form-label">状态</label>
									<div class="layui-input-block">
										<input type="radio" name="status" value="0" checked title="启用">
										<input type="radio" name="status" value="1" title="禁用" >
									</div>
								</div>
								<div class="layui-form-item" pane>
									<label class="layui-form-label"></label>
									<div class="layui-input-block">
										<button class="layui-btn" lay-submit lay-filter="form-submit">
											<i class="layui-icon layui-icon-release"></i>保存修改
										</button>
										<button class="layui-btn layui-btn-normal" lay-filter="form-add" lay-submit>
											<i class="layui-icon layui-icon-add-1"></i>添加为新分类
										</button>
									</div>
								</div>
								<input type="hidden" id="rowId" name="id" value="0">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="moveArticleToCategory" class="dialog-wrap layui-form">
		<div class="layui-form-item">
			<label class="layui-form-label">移动到</label>
			<div class="layui-input-block">
				<select id="moveTocategory" name="move_to" lay-verify="required">
					<?= $treeSelect?>
				</select>
			</div>
		</div>
	</div>


	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js')?>
	<?= Html::jsFile('@static_frontend/js/eleTree.js')?>
	<script type="text/javascript">
		// 权限树
		var pageName = '<?= $name?>',
			currentCategory = {};
		// layui方法
		layui.use(['form', 'layer', 'eleTree'], function () {
			// 操作对象
			var form = layui.form
				, layer = layui.layer
				, eleTree = layui.eleTree
				, $ = layui.jquery;

			// 渲染权限组树
			eleTree.render({
				elem: '#treeList',
				url: '<?=Yii::$app->urlManager->createUrl('article/get-categories')?>',
				type: "get",
//				data: menusTree,
				showCheckbox: false,
				drag: true,
				accordion: true
			});

			/**
			 * 重新加载分类树
			 */
			function reloadTree() {
				eleTree.reload('#treeList', {});
			}

			// 保存分类
			form.on('submit(form-submit)', function (data) {
				var url = '<?= Yii::$app->urlManager->createUrl("article/save-category")?>';
				$.post(
					url,
					data.field,
					function (result) {
						layer.msg(result.message, {time: 2000});
						if (result.code === AJAX_STATUS_SUCCESS) {
							reloadTree();
						}
					},
					'json'
				);

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});
			// 添加为新分类
			form.on('submit(form-add)', function (data) {
				var formData = data.field;
				delete formData.id;
				var url = '<?= Yii::$app->urlManager->createUrl("article/add-category")?>';
				$.post(
					url,
					formData,
					function (result) {
						layer.msg(result.message, {time: 2000});
						if (result.code === AJAX_STATUS_SUCCESS) {
							reloadTree();
							$('form')[0].reset();
						}
					},
					'json'
				);

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});


			eleTree.on("toggleSlide(data1)", function (data) {
				currentCategory = data.currentData;
				currentCategory.category_name = data.currentData.label;
				console.log(currentCategory);
				form.val("actionForm", currentCategory);
			})

			/**
			 * 删除，点击事件
			 */
			$('#btn-delete').click(function () {
				if ($.isEmptyObject(currentCategory)) {
					layer.msg('请选择要删除的分类');
					return false;
				}
				if (currentCategory.children && currentCategory.children.length > 0) {
					layer.msg('该分类含有子分类，不能删除');
					return false;
				}
				layer.confirm('确定删除分类　['+currentCategory.label+']　吗？', function (index) {
					layer.confirm('该分类下的文章，您想？', {
						title: '删除分类',
						skin: 'layui-3-btn',
						btn: ['全部删除','移动到...', '取消'], //按钮
						yes: function () {
							layer.confirm('确定删除分类　['+currentCategory.label+'] 和该分类下的所有文章吗？', function (index) {
								actionDetele('delete', 0);
							});
						},
						btn2 :function () {
							showDialog('移到文章到分类', {
								content: $('#moveArticleToCategory'), yes: function (index, layero) {
									var moveTocategoryId = $('#moveTocategory').val();
									actionDetele('move', moveTocategoryId);
								}
							});
						}
					});

				});
				return false;
			});

			/**
			 * 删除分类
			 * @param afterAction
			 */
			function actionDetele(afterAction, moveTocategoryId) {
				var params = {
					id: currentCategory.id,
					delete_article: 0,
					move_article: 1,
					move_to_category_id: moveTocategoryId,
				};
				if (afterAction == 'delete') {
					params.delete_article = 1;
					params.move_article = 0;
				}
				$.post(
					"<?=Yii::$app->urlManager->createUrl('article/delete-category')?>",
					params,
					function (result) {
						if (result.code === AJAX_STATUS_SUCCESS) {
							reloadTree();
							layer.closeAll();
							currentCategory = {};
						}
						layer.msg(result.message);
					},
					'json',
				)
			}
		});


	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>