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

$name = '评论';
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<title><?= $name?>管理</title>
		<?= $this->render('../public/header.php')?>
		<?php $this->head() ?>
		<style>
			.title-image { cursor: pointer }
			.dialog-wrap { min-height: 200px; text-align: center }
			#showTitleImage img { max-width: 460px; max-height: 700px; }
			/*.layui-layer-page {width: auto !important;}*/
		</style>
	</head>
	<?php $this->beginBody() ?>
	<body class="body">
	<!-- 工具集 -->
	<div class="my-btn-box">
		<span class="fl">
			<a class="layui-btn btn-add btn-default" id="btn-read">全部已读</a>
	        <a class="layui-btn layui-btn-danger radius btn-delect" id="btn-delete-more">批量删除</a>
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
		var pageName = '<?= $name?>',
			layerIndex,
			rowId,
			rowObj;
		// layui方法
		layui.use(['table', 'form', 'layer', 'vip_table', 'util', 'vip_tab'], function () {
			// 操作对象
			var form = layui.form
				, table = layui.table
				, layer = layui.layer
				, vipTable = layui.vip_table
				, vipTab = layui.vip_tab
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
					, {field: 'article_name', title: '名称', width: 255, align: 'center', templet: function (d) {
						var url = "<?=Yii::$app->userCache->get('setting')['site']['host']['value']?>/article-"+d.article_id+".html";
						return '<a style="color: #01AAED" target="_blank" href="'+ url +'">'+ d.article_name +'</a>';
						}}
					, {field: 'is_read', title: '状态', width: 80, templet: function (d) {
						var bg = d.is_delete == 1 ? '' : (d.is_read == 1 ? 'gray' : 'green');
						var text = d.is_delete == 1 ? '已删除' : (d.is_read == 1 ? '已读' : '未读');
						return '<span class="layui-badge layui-bg-'+ bg +'">'+ text +'</span>';
					}, align: 'center'}
					, {field: 'nickname', title: '评论人', width: 120, align: 'center'}
					, {field: 'email', title: '邮箱', width: 140, align: 'center'}
					, {field: 'content', title: '评论内容', /*width: 295,*/ align: 'center'}
					, {field: 'ip', title: 'IP', width: 120, align: 'center'}
					, {field: 'created_at', title: '评论时间', width: 145, templet: function (d) {return util.toDateString(d.created_at * 1000); }, align: 'center'}
					, {fixed: 'right', title: '操作', width: 80, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
				]]
				, url: '<?=Yii::$app->urlManager->createUrl("article/get-comments")?>'
				, method: 'get'
				, page: true
				, limits: [10, 20, 30, 50, 100]
				, limit: 20 //默认采用20
				, size: 'sm' //小尺寸的表格
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
			});
			//监听事件 表格操作按钮
			table.on('tool(dataList)', function (obj) {
				rowObj = obj;
				console.log(obj.data);
				rowId = obj.data.id;
				var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
				if(layEvent === 'delete') { //删除
					actionDelete(obj);
				}
			});

			// 更改是否展示
			form.on('switch(filter-is-show)', function (data) {
				var elem = $(data.elem);
				$.post(
					'<?=Yii::$app->urlManager->createUrl("article/change-tag-status")?>',
					{id: elem.data('row_id')},
					function (result) {
						layer.msg(result.message);
						if (result.code !== AJAX_STATUS_SUCCESS) {
							elem.prop('checked', !data.elem.checked);
							form.render('checkbox') // 实现局部刷新，只刷新switch开关
						}
					}
				)
			});

			/**
			 * 删除
			 * @param obj
			 */
			function actionDelete(obj) {
				layer.confirm('确定删除这个评论吗？', function (index) {
					$.post(
						"<?=Yii::$app->urlManager->createUrl('article/delete-comments')?>",
						{ids: rowId},
						function (result) {
							layer.msg(result.message);
							if (result.code === AJAX_STATUS_SUCCESS) {
								tableIns.reload();
							}
						},
						'json',
					)
				});
			}
			
			/**
			 * 批量删除
			 */
			$('#btn-delete-more').click(function () {
				var checkData = table.checkStatus('dataTable');
				if (checkData.data.length <= 0) {
					layer.msg('请先选择要删除的数据');
					return false;
				}
				layer.confirm('确定删除选中的评论吗？', function (index) {
					var idArray = [];
					for (var i=0; i<checkData.data.length; i++) {
						idArray.push(checkData.data[ i ].id);
					}
//					var ids = idArray.join(',');
					$.post(
						'<?=Yii::$app->urlManager->createUrl("article/delete-comments")?>',
						{ids: idArray},
						function (result) {
							layer.msg(result.message, {time: 2000});
							if (result.code === AJAX_STATUS_SUCCESS) {
								tableIns.reload();
							}
						},
						'json'
					);
				});

//				console.log(checkData.data) //获取选中行的数据
//				console.log(checkData.data.length) //获取选中行数量，可作为是否有选中行的条件
//				console.log(checkData.isAll ) //表格是否全选
			});

			$('#btn-read').click(function () {
				$.post(
					'<?=Yii::$app->urlManager->createUrl("article/comment-read-all")?>',
					function (result) {
						layer.msg(result.message, {time: 2000});
						if (result.code === AJAX_STATUS_SUCCESS) {
							tableIns.reload();
						}
					},
					'json'
				);
			});
			
		});


	</script>
	<!-- 表格操作按钮集 -->
	<script type="text/html" id="barOption">
		<div class="layui-btn-group">
			<button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-danger" lay-event="delete">删除</button>
		</div>
	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>