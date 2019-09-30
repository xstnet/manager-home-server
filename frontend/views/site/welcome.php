<?php

/* @var $this yii\web\View */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Json;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<title>控制台</title>
	<?= $this->render('../public/header.php')?>
	<!--<link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">-->
	<?php $this->head() ?>
	<style>
		.laytable-cell-1-image{  /*最后的pic为字段的field*/
			height: 100%;
			max-width: 100%;
		}
		.notice-text {
			color: #3e8f3e;
			font-size: 14px;
		}
		.layui-form-checkbox[lay-skin=primary] {
			margin: 6px 5px;
		}
		.add-todo {
			padding: 0 0 15px 5px;
		}
		.add-todo-input {
			border-width: 0px;
			border-bottom-width: 1px;
			-webkit-box-shadow: none;
			box-shadow: none;
			border-radius: 4px !important;
		}
		.add-todo-input:hover {
			border-radius: 5px !important;
			border-color: #40a9ff !important;
			border-right-width: 1px !important;
		}
		.todolist-item .layui-form-checked span {
			text-decoration: line-through;
			color: #636060 !important;
		}
		.todolist-item {
			padding: 10px 0 0 5px;
		}
		
		.summary {
			overflow: hidden;
		}
		.summary .layui-card-body {
			color: #ff5722;
			font-size: 20px;
		}
		.summary > div {
			background: #f2f2f2;
		}
		.summary .layui-card {
			margin: 5px;
		}
		.layui-bg-green {
			padding: 1px !important;
		}
	</style>
</head>
<?php $this->beginBody() ?>
<body class="body">
<span id="csrfToken"><?=Yii::$app->request->csrfToken?></span>
<p class="layui-bg-gray" style="padding: 10px; font-size: 14px">
		欢迎你，<span class="notice-text"><?=Yii::$app->user->identity->nickname?>！</span>
		现在是： <span class="notice-text"><?=date('Y年m月d日 H时i分s秒')?></span>,
		本次登录时间为： <span class="notice-text"><?=date('Y-m-d H:i:s', Yii::$app->user->identity->login_at)?></span>,
		上次登录时间为： <span class="notice-text"><?=date('Y-m-d H:i:s', Yii::$app->user->identity->last_login_at)?></span>，
		本次登录IP为： <span class="notice-text"><?=Yii::$app->user->identity->login_ip?></span>，
		上次登录IP为： <span class="notice-text"><?=Yii::$app->user->identity->last_login_ip?></span>
</p>
<p class="layui-bg-gray" style="padding: 0 0 5px 10px; color: #009688 !important;">
	您有 <b style="color: #FF5722"><?=$emailCount?></b> 封邮件待发送
</p>
<p class="layui-bg-gray" style="padding: 0 0 5px 10px; color: #009688 !important;">
	收到 <b style="color: #FF5722"><?=$commentCount?></b> 条新评论
</p>
<!--<p class="layui-bg-red">有 0 封邮件待发送</p>-->
<!--<p class="layui-bg-red">有 0 条新评论</p>-->
<hr class="layui-bg-green">
<div class="layui-row layui-col-space10 my-index-main">
	<div class="summary">
		<div class="layui-col-sm6 layui-col-md3">
			<div class="layui-card">
				<div class="layui-card-header">总访问量</div>
				<div class="layui-card-body">
					<?=$summary['total_count']?> +
				</div>
			</div>
		</div>
		<div class="layui-col-sm6 layui-col-md3">
			<div class="layui-card">
				<div class="layui-card-header">文章数</div>
				<div class="layui-card-body">
					<?=$summary['article_count']?> <small>篇</small>
				</div>
			</div>
		</div>
		<div class="layui-col-sm6 layui-col-md3">
			<div class="layui-card">
				<div class="layui-card-header">7天内发布</div>
				<div class="layui-card-body">
					<?=$summary['article_count_7']?> <small>篇</small>
				</div>
			</div>
		</div>
		<div class="layui-col-sm6 layui-col-md3">
			<div class="layui-card">
				<div class="layui-card-header">30天内发布</div>
				<div class="layui-card-body">
					<?=$summary['article_count_30']?> <small>篇</small>
				</div>
			</div>
		</div>
	</div>
	<hr class="layui-bg-green">
	<div class="layui-col-xs12 layui-col-sm6 layui-col-md6">
		<div id="summaryCount" style="height: 320px;"></div>
		
	</div>
	<div class="layui-col-xs12 layui-col-sm6 layui-col-md6">
		<div id="summaryCategory" style="height: 330px;"></div>
	</div>
	<hr class="layui-bg-green">
	<div class="layui-col-xs12 layui-col-sm6 layui-col-md6">
		<div class="layui-collapse">
			<div class="layui-colla-item">
				<h2 class="layui-colla-title">待办事项</h2>
				<div class="layui-colla-content layui-show">
					<div>
						<form class="layui-form" id="todoForm" action="">
							<div class="add-todo">
								<input type="text" id="todoName" placeholder="Add new todo, press Enter to submit" autocomplete="off" class="layui-input add-todo-input">
							</div>
							<div class="todolist-item" id="todolistItem"></div>
							<div id="todoPage"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="layui-col-xs12 layui-col-sm6 layui-col-md6">
		<div class="layui-collapse">
			<div class="layui-colla-item">
				<h2 class="layui-colla-title">登录历史</h2>
				<div class="layui-colla-content layui-show">
					<table class="layui-table">
						<colgroup>
							<col width="150">
							<col width="200">
							<col>
						</colgroup>
						<thead>
						<tr>
							<th>昵称</th>
							<th>IP</th>
							<th>登录时间</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($loginHistory as $item):?>
						<tr>
							<td><?=$item['nickname']?></td>
							<td><?=$item['login_ip']?></td>
							<td><?=date('Y-m-d H:i:s', $item['login_at'])?></td>
						</tr>
						<?php endforeach;?>
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
<!--	<div class="layui-col-xs12 layui-col-sm6 layui-col-md6">-->
<!--		<div class="layui-collapse">-->
<!--			<div class="layui-colla-item">-->
<!--				<h2 class="layui-colla-title">表格</h2>-->
<!--				<div class="layui-colla-content layui-show">-->
<!---->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
</div>
<!--<div class="layui-row layui-col-space10 my-index-main">-->
<!--	-->
<!--</div>-->
<?= $this->render('../public/footer_js.php')?>
<?= Html::jsFile('@static_frontend/js/index.js?v=' . Yii::$app->params['static_file_t'])?>
<?= Html::jsFile('@static_frontend/plugins/echarts/echarts.min.js?')?>
<?= Html::jsFile('@static_frontend/plugins/echarts/themes/vintage.js?')?>
<script type="text/javascript">
	var todos = <?=Json::encode($todos)?>;

	var myChart = echarts.init(document.getElementById('summaryCount'));
	var myChartCategory = echarts.init(document.getElementById('summaryCategory'));

	// 指定图表的配置项和数据
	var option = {
		title: {
			text: '7天内访问信息'
		},
		tooltip: {},
		legend: {
			data:['访问数量', 'IP']
		},
		xAxis: {
			data: <?=Json::encode($chartDayCount['date'])?>
		},
		yAxis: {},
		series: [
			{
				name: '访问数量',
				type: 'bar',
				data: <?=Json::encode($chartDayCount['day'])?>
			},
			{
				name: 'IP',
				type: 'bar',
				data: <?=Json::encode($chartDayCount['ip'])?>
			}
		]
	};

	// 使用刚指定的配置项和数据显示图表。
	myChart.setOption(option, 'vintage');
	var chartCategorydata = <?=Json::encode($chartCategory)?>;
	var optionCategory = {
		title : {
			text: '分类文章数量',
			subtext: '分类文章占比',
			x: 'center'
		},
		tooltip : {
			trigger: 'item',
			formatter: "{a} <br/>{b} : {c} ({d}%)"
		},
		legend: {
			type: 'scroll',
			orient: 'vertical',
			right: 10,
			top: 20,
			bottom: 20,
			data: chartCategorydata.legendData,
			selected: chartCategorydata.selected
		},
		series : [
			{
				name: '分类',
				type: 'pie',
				radius : '55%',
				center: ['40%', '50%'],
				data: chartCategorydata.seriesData,
				itemStyle: {
					emphasis: {
						shadowBlur: 10,
						shadowOffsetX: 0,
						shadowColor: 'rgba(0, 0, 0, 0.5)'
					}
				}
			}
		]
	};
	myChartCategory.setOption(optionCategory, 'vintage');

	layui.use(['element', 'form', 'table', 'layer', 'laypage', 'vip_tab'], function () {
		var form = layui.form
			, layer = layui.layer
			, vipTab = layui.vip_tab
			, $ = layui.jquery
			, laypage = layui.laypage;


		// 打开选项卡
		$('.my-nav-btn').on('click', function () {
			if ($(this).attr('data-href')) {
				//vipTab.add('','标题','路径');
				vipTab.add($(this),'<i class="layui-icon">'+$(this).find("button").html()+'</i>'+$(this).find('p:last-child').html(),$(this).attr('data-href'));
			}
		});
		// todolist 分页
		laypage.render({
			elem: 'todoPage', //注意，这里的 todoPage 是 ID，不用加 # 号
			count: todos.total, //数据总数，从服务端得到
			limit: 7,
			first: false,
			last: false,
			prev: '<em>←</em>',
			next: '<em>→</em>',
			jump: function (obj, first) {
				//首次不执行
				if(!first) {
					var url = '<?=Yii::$app->urlManager->createUrl('todo/get-todos')?>';
					$.getJSON(
						url,
						{page: obj.curr, pageSize: obj.limit},
						function (result) {
							if (result.code === AJAX_STATUS_SUCCESS) {
								todos = result.data;
								genderTodoList();
								return false;
							}
							layer.msg(result.message);
						}
					)
				}
			}
		});

		function genderTodoList() {
			$('#todolistItem').html('');
			if (todos.list.length > 0) {
				var todoHtml = '',
					item,
					checked;
				for (var i=0; i<todos.list.length; i++) {
					item = todos.list[i];
					checked = (item.status == 1 ? "checked" : "");
					todoHtml += '<input type="checkbox" lay-filter="todoitem" value="'+item.id+'" title="'+item.name+'" '+checked+' lay-skin="primary">';
					todoHtml += '<br/>';
				}
				$('#todolistItem').html(todoHtml);
				form.render('checkbox');
			}
		}
		// 添加 newtodo
		$('form').on('submit', function () {
			var todoName = $.trim($('#todoName').val());
			if (todoName.length <= 0) {
				return false;
			}
			$.post(
				'<?=Yii::$app->urlManager->createUrl('todo/add-todo')?>',
				{name: todoName},
				function (result) {
					layer.msg(result.message);
					if (result.code === AJAX_STATUS_SUCCESS) {
						var newTodo = '<input type="checkbox" lay-filter="todoitem" value="'+result.data.id+'" title="'+todoName+'" lay-skin="primary"> <br/>'
						$('#todolistItem').prepend(newTodo);
						$('#todoName').val('')
						form.render('checkbox');
					}
				},
				'json'
			);
			return false;
		})

		//更新 totod状态
		form.on('checkbox(todoitem)', function (data) {
			$.post(
				'<?=Yii::$app->urlManager->createUrl('todo/change-status')?>',
				{id: data.value},
				function (result) {
					layer.msg(result.message);
					if (result.code && result.code !== AJAX_STATUS_SUCCESS) {
						$(data.elem).prop('checked', !data.elem.checked);
						form.render('checkbox');
					}
				},
				'json'
			)
		});
		genderTodoList();
		
	});

</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>