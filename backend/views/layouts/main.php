<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

$request = Yii::$app->request;
$searchKeyword = trim($request->get('s', ''));
if (!empty($searchKeyword)) {
	$searchKeyword = Html::encode($searchKeyword);
}
$userCache = Yii::$app->userCache;
// 导航高亮
if (!isset($this->params['active_menu'])) {
	$this->params['active_menu'] = 'index';
}
if (!isset($this->params['keyword'])) {
	$this->params['keyword'] = '个人博客,php博客,程序员博客,php程序员博客,博客网站,徐善通博客,徐善通的个人博客';
}
if (!isset($this->params['description'])) {
	$this->params['description'] = '徐善通的技术笔记,分享一些php问题的解决方案';
}
$assetVersion = '1.0019';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
	<title><?= $this->title ?Html::encode($this->title) . '-'  : ''?> <?=Yii::$app->userCache->get('setting')['site']['name']['value']?></title>
	<meta name="keywords" content="<?=$this->params['keyword']?>"/>
	<meta name="description" content="<?=$this->params['description']?>"/>
	<!-- Bootstrap -->
<!--	<link href="/static/css/bootstrap.min.css" rel="stylesheet">-->
	<link href="/static/css/style.min.css?v=<?=$assetVersion?>" rel="stylesheet">
    <?php if (YII_ENV == 'prod'):?>
	<script>
		var _hmt = _hmt || [];
		(function() {
			var hm = document.createElement("script");
			hm.src = "//hm.baidu.com/hm.js?e2452306e2c4c96f1da5662a7178b0b1";
			var s = document.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(hm, s);
		})();
	</script>
    <?php endif;?>

	<!-- HTML5 shim 和 Respond.js 是为了让 IE8 支持 HTML5 元素和媒体查询（media queries）功能 -->
	<!-- 警告：通过 file:// 协议（就是直接将 html 页面拖拽到浏览器中）访问页面时 Respond.js 不起作用 -->
	<!--[if lt IE 9]>
	<script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
	<![endif]-->
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="main">
	<div class="container-fluid">
		<!-- Header Start -->
		<div class="row header-bg">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-md-12">
						<div class="header">
							<div class="row">
								<div class="col-md-5">
									<h1 class="site-title"><a href="/">徐善通的随笔</a></h1>
									<h4 class="my-motto">千里之行, 始于足下</h4>
								</div>
								<div class="col-md-3">
									<form action="/search.html" method="get">
									<div class="input-group" style="margin-top: 20px">
										<input name="s" type="text" value="<?=$searchKeyword?>" class="form-control" placeholder="Search for...">
										<span class="input-group-btn"><button class="btn btn-default" type="submit">Go!</button></span>
									</div>
									</form>
								</div>
								<div class="col-md-3"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12  col-md-12 col-sm-12 col-xs-12">
						<div class="nav">
							<ul>
								<li<?=$this->params['active_menu'] == 'index' ? ' class="active"' : ''?>><a href="/">首页</a></li>
								<li<?=$this->params['active_menu'] == 'note' ? ' class="active"' : ''?>><a href="/note.html">随笔</a></li>
								<li<?=$this->params['active_menu'] == 'archive' ? ' class="active"' : ''?>><a href="/archive.html">归档</a></li>
								<li<?=$this->params['active_menu'] == 'about' ? ' class="active"' : ''?>><a href="/about.html">关于我</a></li>
								<li<?=$this->params['active_menu'] == 'message' ? ' class="active"' : ''?>><a href="/message.html">留言</a></li>
								<li<?=$this->params['active_menu'] == 'rss' ? ' class="active"' : ''?>><a target="_blank" href="/rss.xml">订阅</a></li>
							</ul>
						</div>
					</div>
<!--					<div class="col-lg-3"></div>-->
				</div>
			</div>
		</div>
		<!-- Header End -->
		<hr class="hr margin-t-0">
		<!-- Content Start -->
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<?= $content?>
					<div class="col-md-2">
						<div class="content-right category-list">
<!--							<div class="margin-t-20"></div>-->
<!--							<h4>关于我</h4>-->
<!--							<div class="margin-t-20"></div>-->
<!--							<hr class="hr margin-0">-->
							<div class="text-center">
								<img src="/static/images/head.gif" alt="avatar" class="img-circle">
								<h3>徐善通</h3>
								<h5>一枚热爱技术的后端程序猿</h5>
							</div>
							<div style="margin-top: 30px"></div>
							<h4>随笔分类</h4>
							<hr class="hr margin-0">
							<div class="margin-t-5"></div>
							<?= \common\helpers\Helpers::renderCategoryTree($userCache->get('articleCategoryTree'))?>
							<div class="margin-t-20"></div>
							<h4>标签云</h4>
							<hr class="hr margin-0">
							<div class="tag-list margin-t-5">
								<?php
								$tagMap = ['default', 'success', 'info', 'warning', 'danger'];
								foreach ($userCache->get('tagList') as $k => $tag) {
									echo "<a href=\"/tag/{$tag['name']}.html\"><span class=\"label label-{$tagMap[$k%5]}\">{$tag['name']}</span></a> ";
								}
								?>
							</div>
							<div class="margin-t-20"></div>
							<h4>最新文章</h4>
							<hr class="hr margin-0">
							<div>
								<?php foreach ($userCache->get('latestArticle') as $item) :?>
								<h5><a href="/article-<?=$item['id']?>.html"><?=$item['title']?></a></h5>
								<?php endforeach;?>
							</div>
							<div class="margin-t-20"></div>
							<h4>关于</h4>
							<hr class="hr margin-0">
							<div class="margin-t-5">
								<p>已运行:  <strong><?=ceil((time()-strtotime('2015-12-01'))/86400)?></strong>天</p>
								<p>访问量: <?=$userCache->get('totalCount')?></p>
								<p>在线人数: 1</p>
								<p>QQ: 792539542</p>
								<p>邮箱: shantongxu@qq.com</p>
								<p><a target="_blank" href="http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=MUJZUF9FXl9WSURxQEAfUl5c" style="text-decoration:none;"><img src="http://rescdn.qqmail.com/zh_CN/htmledition/images/function/qm_open/ico_mailme_01.png"/></a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Content End -->

		<!-- Footer Start -->
		<footer>
			<p class="text-center margin-0">Copyrights © 2016-2019 <a href="http://xstnet.com">醉丶春风</a> , All rights reserved. 皖ICP备15015582号-1</p>
		</footer>
		<!-- Footer End -->
	</div>
</div>
<div title="回到顶部" id="toTop"><i style="font-size: 32px" class="glyphicon glyphicon-chevron-up"></i></div>

<!-- jQuery (Bootstrap 的所有 JavaScript 插件都依赖 jQuery，所以必须放在前边) -->
<!--<script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>-->
<!-- 加载 Bootstrap 的所有 JavaScript 插件。你也可以根据需要只加载单个插件。 -->
<!--<script src="/static/js/bootstrap.min.js"></script>-->


<?php $this->endBody() ?>
<script src="/counter.html"></script>
<script>
	var toTop = document.getElementById("toTop");
	toTop.onclick = function(){
		document.documentElement.scrollTop = document.body.scrollTop =0;
	}
</script>
</body>
</html>
<?php $this->endPage() ?>
