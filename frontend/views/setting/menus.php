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

$name = '菜单';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
		<?= $this->render('../public/header.php')?>
		<?= Html::cssFile('@static_frontend/css/eleTree.css')?>
		<?php $this->head() ?>
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
					<div class="layui-card-header">菜单列表</div>
					<div class="layui-card-body">
						<!--菜单树-->
						<div class="eleTree" id="treeList" lay-filter="data1"></div>
					</div>
				</div>
			</div>
			<div class="layui-col-md5">
				<div class="layui-card">
					<div class="layui-card-header">菜单信息</div>
					<div class="layui-card-body">
						<div style="padding-bottom: 20px;">
							<form style="padding-top: 30px" class="layui-form" lay-filter="actionForm" action="">
								<div class="layui-form-item">
									<label class="layui-form-label">所属菜单</label>
									<div class="layui-input-block">
										<select name="parent_id" lay-verify="required">
											<option value="0">一级菜单</option>
											<?= $treeSelect?>
										</select>
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">名称</label>
									<div class="layui-input-block">
										<input type="text" name="name" required  lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">路由</label>
									<div class="layui-input-block">
										<input type="text" name="url" placeholder="请输入路由" autocomplete="off" class="layui-input">
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">排序值</label>
									<div class="layui-input-inline">
										<input type="text" name="sort_value" value="30" lay-verify="required|number" placeholder="请输入排序值" autocomplete="off" class="layui-input">
										<div class="layui-form-mid layui-word-aux">按照从小到大的顺序排列</div>
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">图标</label>
									<div class="layui-input-inline">
										<input type="text" name="icon" value="" placeholder="图标 class" autocomplete="off" class="layui-input">
										<div class="layui-form-mid layui-word-aux">
											<a href="https://www.layui.com/doc/element/icon.html" target="_blank">参考 https://www.layui.com/doc/element/icon.html</a>
										</div>
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
											<i class="layui-icon layui-icon-add-1"></i>添加为新菜单
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


	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js?v='. Yii::$app->params['static_file_t'])?>
	<?= Html::jsFile('@static_frontend/js/eleTree.js')?>
	<script type="text/javascript">
		// 权限树
		var pageName = '<?= $name?>',
			menusTree = {},
			currentMenu = {};
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
				url: '<?=Yii::$app->urlManager->createUrl('setting/get-menus')?>',
				type: "get",
//				data: menusTree,
				showCheckbox: false,
				drag: true,
				accordion: true
			});

			/**
			 * 重新加载菜单树
			 */
			function reloadTree() {
				eleTree.reload('#treeList', {});
			}

			// 保存菜单
			form.on('submit(form-submit)', function (data) {
				var url = '<?= Yii::$app->urlManager->createUrl("setting/save-menu")?>';
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
			// 添加为新菜单
			form.on('submit(form-add)', function (data) {
				var formData = data.field;
				delete formData.id;
				var url = '<?= Yii::$app->urlManager->createUrl("setting/add-menu")?>';
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
				currentMenu = data.currentData;
				currentMenu.name = data.currentData.label;
				currentMenu.url = data.currentData.router;
				console.log(currentMenu);
				form.val("actionForm", currentMenu);
			})

			/**
			 * 删除菜单
			 */
			$('#btn-delete').click(function () {
				if ($.isEmptyObject(currentMenu)) {
					layer.msg('请选择要删除的菜单');
					return false;
				}
				if (currentMenu.children && currentMenu.children.length > 0) {
					layer.msg('该菜单含有子菜单，不能删除');
					return false;
				}
				layer.confirm('确定删除菜单　['+currentMenu.label+']　吗？', function (index) {
					$.post(
						"<?=Yii::$app->urlManager->createUrl('setting/delete-menu')?>",
						{id: currentMenu.id},
						function (result) {
							layer.msg(result.message);
							if (result.code === AJAX_STATUS_SUCCESS) {
								reloadTree();
								layer.close(index);
								currentMenu = {};
							}
						},
						'json',
					)
				});
				return false;
			})
		});


	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>