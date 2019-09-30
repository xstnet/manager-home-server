<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xstnet
 * Date: 18-10-23
 * Time: 下午4:57
 */


/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Json;

$name = '操作日志';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
		<?= $this->render('../public/header.php')?>
		<?php $this->head() ?>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">
	<!-- 工具集 -->
	<div class="my-btn-box">
    <span class="fl">
        <button class="layui-btn btn-add btn-default btn-disabled" id="btn-add" disabled="disabled">操作日志</button>
    </span>
		<span class="fr">
        <span class="layui-form-label">搜索条件：</span>
        <div class="layui-input-inline">
            <input type="text" autocomplete="off" placeholder="请输入搜索条件" class="layui-input">
        </div>
        <button class="layui-btn mgl-20">查询</button>
			<a class="layui-btn btn-add btn-default" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></a>
    </span>
	</div>
	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

	<!-- 表格 -->
	<div id="dataTable" lay-filter="dataList"></div>


	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js')?>
	<script type="text/javascript">
		// 权限树
		// layui方法
		layui.use(['table', 'form', 'layer', 'vip_table', 'util'], function () {
			// 操作对象
			var form = layui.form
				, table = layui.table
				, layer = layui.layer
				, vipTable = layui.vip_table
				, util = layui.util
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
					, {field: 'title', title: '操作项目', width: 200, align: 'center'}
					, {field: 'nickname', title: '昵称', width: 150, align: 'center'}
					, {field: 'ip', title: 'IP', width: 120, align: 'center'}
					, {field: 'route', title: '路由', width: 200, align: 'center'}
					, {field: 'url', title: 'Url', width: 300, align: 'center'}
					, {field: 'params', title: '参数', align: 'center'}
					, {field: 'created_at', title: '操作时间', width: 180, templet: function (d) {return util.toDateString(d.created_at * 1000); }, align: 'center'}
				]]
				, url: '<?=Yii::$app->urlManager->createUrl("system-log/get-logs")?>'
				, method: 'get'
				, page: true
				, limits: [10, 20, 30, 50, 100]
				, limit: 20 //默认采用20
				, loading: false
				, page: {
					layout: ['prev', 'page', 'next', 'skip', 'count', 'refresh','limit', ]
				}
				, parseData: function (res) { //res 即为原始返回的数据
					return {
						"code": res.code, //解析接口状态
						"msg": res.message, //解析提示文本
						"count": res.data.total, //解析数据长度
						"data": res.data.list //解析数据列表
					};
				}
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