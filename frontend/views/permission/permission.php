<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Json;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<title>权限管理</title>
	<?= $this->render('../public/header.php')?>
	<?php $this->head() ?>
	<style>

	</style>
</head>
<?php $this->beginBody() ?>
<body class="body">
<!-- 工具集 -->
<div class="my-btn-box">
    <span class="fl">
<!--        <a class="layui-btn layui-btn-danger radius btn-delect" id="btn-delete-all">批量删除</a>-->
        <a class="layui-btn btn-add btn-default" id="btn-add">添加权限</a>
        <a class="layui-btn btn-add btn-default" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></a>
    </span>
	<span class="fr">
        <span class="layui-form-label">搜索条件：</span>
        <div class="layui-input-inline">
            <input type="text" autocomplete="off" placeholder="请输入搜索条件" class="layui-input">
        </div>
        <button class="layui-btn mgl-20">查询</button>
    </span>
</div>
<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

<!-- 表格 -->
<div id="dataTable" lay-filter="dataList"></div>
<!--编辑/添加角色-->
<div id="roleInfoDlg" class="dialog-wrap" style="min-height: 200px">
	<form style="padding-top: 30px" class="layui-form layui-form-pane" lay-filter="menusForm" action="">
		<div class="layui-form-item">
			<label class="layui-form-label">所属菜单</label>
			<div class="layui-input-block">
				<select name="menu_id" lay-verify="required">
					<option value=""></option>
					<?= $treeSelect?>
					<option value="0">其他</option>
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
				<input type="text" name="url" required lay-verify="required" value="" placeholder="请输入路由" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">描述</label>
			<div class="layui-input-block">
				<input type="text" name="description" required  placeholder="请输入描述" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item" pane>
			<label class="layui-form-label">状态</label>
			<div class="layui-input-block">
				<input type="radio" name="status" value="0" checked title="启用">
				<input type="radio" name="status" value="1" title="禁用" >
			</div>
		</div>
		<input type="hidden" id="rowId" name="id" value="0">
		<button id="submitBtn" lay-submit="" lay-filter="form-submit"></button>
	</form>
</div>

<?= $this->render('../public/footer_js.php')?>
<?= Html::jsFile('@static_frontend/js/index.js')?>
<script type="text/html" id="formatStatus">
	{{#
	var fn = function () {
		return d.status == 0 ? 'checked' : '';
	};
	}}
	<input type="checkbox" lay-filter="filter-status" value="1"  name="status" data-permission_id="{{d.id}}" lay-skin="switch" lay-text="启用|禁用" {{ fn() }}>
</script>
<script type="text/javascript">
	// 权限树
	var menus = <?= Json::encode($menus)?>,
		layerIndex,
		permissionID,
		rowObj;
	// layui方法
	layui.use(['table', 'form', 'layer', 'vip_table', 'util'], function () {
		// 操作对象
		var form = layui.form
			, table = layui.table
			, layer = layui.layer
			, vipTable = layui.vip_table
			, util = layui.util
			, treeSelect = layui.treeSelect
			, $ = layui.jquery;

		// 表格渲染
		var tableIns = table.render({
			elem: '#dataTable'                  //指定原始表格元素选择器（推荐id选择器）
			//, height: vipTable.getFullHeight()    //容器高度
			, even: true
			, text: '暂无数据'
			, cols: [[
				{type:'checkbox'}
				, {field: 'id', title: 'ID', width: 80, align: 'center'}
				, {field: 'name', title: '名称', width: 150, align: 'center'}
				, {field: 'description', title: '描述', width: 200, align: 'center'}
				, {field: 'menu_id', title: '所属菜单', width: 120, align: 'center', templet: function (d) {
					return (menus[ d.menu_id ] ? menus[ d.menu_id ].name : '');
				}}
				, {field: 'url', title: 'url', width: 300, align: 'center'}
				, {field: 'status', title: '状态', width: 100, templet: '#formatStatus', align: 'center'}
				, {field: 'created_at', title: '添加时间', width: 180, templet: function (d) {return util.toDateString(d.created_at * 1000); }, align: 'center'}
				, {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
			]]
			, url: '<?=Yii::$app->urlManager->createUrl("permission/get-permissions")?>'
			, method: 'get'
			, page: true
			, limits: [10, 20, 30, 50, 100]
			, limit: 20 //默认采用20
			, page: {
				layout: ['prev', 'page', 'next', 'skip', 'count', 'refresh','limit', ]
			}
			, loading: false
			, parseData: function (res) { //res 即为原始返回的数据
				return {
					"code": res.code, //解析接口状态
					"msg": res.message, //解析提示文本
					"count": res.data.total, //解析数据长度
					"data": res.data.list //解析数据列表
				};
			}
//			, done: function (res, curr, count) {
//
//			}
		});
		//监听事件 表格操作按钮
		table.on('tool(dataList)', function (obj) {
			rowObj = obj;
			permissionID = obj.data.id;
			var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
			if(layEvent === 'delete') { //删除
				deleteMenus(obj);
			} else if(layEvent === 'edit') { //编辑
				showMenusInfo('edit');
			}
		});

		// Change Status
		form.on('switch(filter-status)', function (data) {
			var elem = $(data.elem);
			$.post(
				'<?=Yii::$app->urlManager->createUrl("permission/change-status")?>',
				{id: elem.data('permission_id')},
				function (result) {
					layer.msg(result.message);
					if (result.code !== AJAX_STATUS_SUCCESS) {
						elem.prop('checked', !data.elem.checked);
						form.render('checkbox') // 实现局部刷新，只刷新switch开关
					}
				}
			)
		});

		// 添加按钮 点击事件
		$('#btn-add').click(function () {
			showMenusInfo('add');
			$('#rowId').val(0);
		});

		function showMenusInfo(type) {
			var title = type === 'edit' ? '编辑权限' : '添加权限';
			if (type === 'edit') {
				form.val("menusForm", rowObj.data);
			} else {
				$('form')[0].reset();
			}
			showDialog(title, {
				content: $('#roleInfoDlg'), yes: function (index, layero) {
					layerIndex = index;
					$('#submitBtn').click();
				}
			});
		}

		/**
		 * 删除
		 * @param obj
		 */
		function deleteMenus(obj) {
			layer.confirm('确定删除这个权限吗', function (index) {
				$.post(
					"<?=Yii::$app->urlManager->createUrl('permission/delete-permission')?>",
					{id: permissionID},
					function (result) {
						layer.msg(result.message);
						if (result.code === AJAX_STATUS_SUCCESS) {
							obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
							layer.close(index);
						}
					},
					'json',
				)
				//向服务端发送删除指令
			});
		}

		// 提交表单
		form.on('submit(form-submit)', function (data) {
			var url = '<?= Yii::$app->urlManager->createUrl("permission/add-permission")?>';
			if (data.field.id > 0) {
				url = '<?= Yii::$app->urlManager->createUrl("permission/save-permission")?>';
			}
			$.post(
				url,
				data.field,
				function (result) {
					layer.msg(result.message, {time: 2000});
					if (result.code === AJAX_STATUS_SUCCESS) {
						if (data.field.id > 0) {
							rowObj.update(data.field);
							form.render('checkbox') // 实现局部刷新，只刷新switch开关
							layer.close(layerIndex);
						} else {
							tableIns.reload();
						}
					}

				},
				'json'
			);
			return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
		});

	});


</script>
<!-- 表格操作按钮集 -->
<script type="text/html" id="barOption">
	<div class="layui-btn-group">
		<button class="layui-btn layui-btn-sm layui-btn-radius" lay-event="edit">编辑</button>
		<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-danger" lay-event="delete">删除</button>
	</div>
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>