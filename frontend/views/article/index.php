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

$name = '文章';
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
	
	<?= $this->render('../public/searchFields.php', ['searchFields' => $searchFields, 'select_category_id' => $treeSelect])?>
	
	<hr class="layui-bg-green">
	<!-- 工具集 -->
	<div class="my-btn-box">
		<span class="fl">
	        <a class="layui-btn layui-btn-danger radius btn-delect layui-btn-sm" id="btn-delete-more">批量删除</a>
			<a class="layui-btn btn-add btn-default layui-btn-sm" id="btn-add">发布<?= $name?></a>
		</span>
		<span class="fr">
				<a class="layui-btn btn-add btn-default layui-btn-sm" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></a>
		</span>
	</div>
	
	<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>

	<!-- 表格 -->
	<div id="dataTable" lay-filter="dataList" lay-size="sm"></div>
	<!--编辑/添加角色-->
	<div id="addDlg" class="dialog-wrap" style="min-height: 200px">
		<form style="padding-top: 30px" class="layui-form" lay-filter="actionForm" action="">
			<div class="layui-form-item">
				<label class="layui-form-label">标题</label>
				<div class="layui-input-block">
					<input type="text" name="title" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">作者</label>
				<div class="layui-input-block">
					<input type="text" name="author" required  lay-verify="required" placeholder="请输入作者" autocomplete="off" class="layui-input">
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
				<label class="layui-form-label">排序值</label>
				<div class="layui-input-inline">
					<input type="text" name="sort_value" value="30" lay-verify="required|number"  placeholder="请输入排序值" autocomplete="off" class="layui-input">
					<div class="layui-form-mid layui-word-aux">按照从小到大的顺序排列</div>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">来源</label>
				<div class="layui-input-block">
					<input type="text" name="source"  placeholder="请输入来源" autocomplete="off" class="layui-input">
				</div>
			</div>
			<input type="hidden" id="rowId" name="id" value="0">
			<button id="submitBtn" lay-submit="" lay-filter="form-submit"></button>
		</form>
	</div>
	<div id="showTitleImage" class="dialog-wrap">
		<img src="" alt="">
	</div>

	<?= $this->render('../public/footer_js.php')?>
	<?= Html::jsFile('@static_frontend/js/index.js?v='. Yii::$app->params['static_file_t'])?>
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
				// , height: vipTable.getFullHeight()    //容器高度
				, even: true
				, text: '暂无数据'
				, cols: [[
					{type:'checkbox'}
					, {field: 'id', title: 'ID', width: 40, align: 'center'}
					, {field: 'title', title: '标题', width: 310, align: 'center', templet: function (d) {
						var img = '';
						if (d.title_image) {
							img += '<img onclick="showTitleImage(\''+d.title_image+'\')" class="title-image" data-src="'+ d.title_image +'" src="<?=Yii::getAlias("@static_frontend")?>/images/img-icon-16.png"> ';
						}
						return img + '<span style=\''+ d.title_style +'\'>'+ d.title +'</span>';
					}}
					, {field: 'category_name', title: '分类', width: 100, align: 'center'}
					, {field: 'nickname', title: '发布人', width: 80, align: 'center'}
					, {field: 'author', title: '作者', width: 80, align: 'center'}
					, {field: 'hits', title: '查看次数', width: 80, align: 'center'}
					// , {field: 'sort_value', title: '排序', width: 60, align: 'center'}
					, {field: 'source', title: '来源', width: 60, align: 'center'}
					, {field: 'is_show', title: '是否展示', width: 100, templet: function (d) {
						var checked = d.is_show == 1 ? 'checked' : '';
						return '<input type="checkbox" lay-filter="filter-is-show" value="1" data-row_id="'+ d.id +'" lay-skin="switch" lay-text="显示|不显示" '+ checked +' >';
					}, align: 'center'}
					, {field: 'is_allow_comment', title: '允许评论', width: 100, templet: function (d) {
						var checked = d.is_allow_comment == 1 ? 'checked' : '';
						return '<input type="checkbox" lay-filter="filter-is-allow-comment" value="1" data-row_id="'+ d.id +'" lay-skin="switch" lay-text="允许|不允许" '+ checked +' >';
						}, align: 'center'}
					, {field: 'created_at', title: '发布时间', width: 145, templet: function (d) {return util.toDateString(d.created_at * 1000); }, align: 'center'}
					, {field: 'updated_at', title: '更新时间', width: 145, templet: function (d) {return util.toDateString(d.created_at * 1000); }, align: 'center'}
					, {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barOption'} //这里的toolbar值是模板元素的选择器
				]]
				, url: '<?=Yii::$app->urlManager->createUrl("article/get-articles")?>'
				, method: 'get'
				, page: true
				, limits: [10, 20, 30, 50, 100]
				, limit: 20 //默认采用20
				, page: {
					layout: ['prev', 'page', 'next', 'skip', 'count', 'refresh','limit', ]
				}
				, size: 'sm' //小尺寸的表格
				// , skin: 'line'
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
				rowId = obj.data.id;
				var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
				if(layEvent === 'delete') { //删除
					actionDelete(obj);
				} else if(layEvent === 'edit') { //编辑
					var url = '<?=Yii::$app->urlManager->createUrl(["article/edit", 'id' => ""])?>' + rowId;
					vipTab.add('', '<i class="layui-icon layui-icon-edit"></i><span>编辑文章</span>', url);
				} else if (layEvent === 'brief-edit') { // 快速编辑
					actionShow();
				}
			});

			// 更改是否展示
			form.on('switch(filter-is-show)', function (data) {
				var elem = $(data.elem);
				$.post(
					'<?=Yii::$app->urlManager->createUrl("article/change-is-show")?>',
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

			// 更改是否允许评论
			form.on('switch(filter-is-allow-comment)', function (data) {
				var elem = $(data.elem);
				$.post(
					'<?=Yii::$app->urlManager->createUrl("article/change-is-allow-comment")?>',
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

			// 添加按钮 点击事件
			$('#btn-add').click(function () {
				vipTab.add('', '<i class="layui-icon layui-icon-add-1"></i><span>发布文章</span>', '<?=Yii::$app->urlManager->createUrl('article/add')?>');
				$('#rowId').val(0);
			});

			function actionShow() {
				var title = '快速编辑';
				$('form')[0].reset();
				form.val("actionForm", rowObj.data);
				form.render('checkbox');

				showDialog(title, {
					content: $('#addDlg'), yes: function (index, layero) {
						layerIndex = index;
						$('#submitBtn').click();
					}
				});
			}

			/**
			 * 删除
			 * @param obj
			 */
			function actionDelete(obj) {
				layer.confirm('确定删除这篇文章吗？', function (index) {
					$.post(
						"<?=Yii::$app->urlManager->createUrl('article/delete-article')?>",
						{id: rowId},
						function (result) {
							layer.msg(result.message);
							if (result.code === AJAX_STATUS_SUCCESS) {
								obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
								layer.close(index);
							}
						},
						'json',
					)
				});
			}

			// 快速编辑， 提交
			form.on('submit(form-submit)', function (data) {
				var url = '<?= Yii::$app->urlManager->createUrl("article/save-article-brief")?>';
				$.post(
					url,
					data.field,
					function (result) {
						layer.msg(result.message, {time: 2000});
						if (result.code === AJAX_STATUS_SUCCESS) {
							tableIns.reload();
						}
					},
					'json'
				);
				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});

			/**
			 * 批量删除
			 */
			$('#btn-delete-more').click(function () {
				var checkData = table.checkStatus('dataTable');
				if (checkData.data.length <= 0) {
					layer.msg('请先选择要删除的数据');
					return false;
				}
				layer.confirm('确定删除选中的文章吗？', function (index) {
					var idArray = [];
					for (var i=0; i<checkData.data.length; i++) {
						idArray.push(checkData.data[ i ].id);
					}
//					var ids = idArray.join(',');
					$.post(
						'<?=Yii::$app->urlManager->createUrl("article/delete-articles")?>',
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



		});

		/**
		 * 查看标题图片
		 */
		function showTitleImage(src) {
			$('#showTitleImage img').attr('src', src);
			showDialog('查看图片', {
				content: $('#showTitleImage'),
				btn: [],
//				offset: 'auto',
			});
			return false;
		}


	</script>
	<!-- 表格操作按钮集 -->
	<script type="text/html" id="barOption">
		<div class="layui-btn-group">
			<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-xs" lay-event="brief-edit">快速编辑</button>
			<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal layui-btn-xs" lay-event="edit">编辑</button>
			<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-danger layui-btn-xs" lay-event="delete">删除</button>
				<a class="layui-btn layui-btn-sm layui-btn-radius layui-btn-xs" lay-even="view" target="_blank" href="<?=Yii::$app->userCache->get('setting')['site']['host']['value']?>/article-{{d.id}}.html">查看</a>
		</div>
	</script>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>