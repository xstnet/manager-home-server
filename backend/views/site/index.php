<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title =  '';
$userCache = Yii::$app->userCache;
if (isset($active_menu)) {
	$this->params['active_menu'] = $active_menu;
}
//?>
<div class="col-md-9 content-left">
	<?php if (isset($breadcrumb)) {
		echo \common\helpers\Helpers::renderBreadcrumb($breadcrumb);
	}?>
	<?php if (!empty($articleList)) :?>
		<?php foreach($articleList as $item) :?>
			<div class="list-item">
				<h4><a href="/article-<?= $item['id']?>.html"><?= $item['title']?></a></h4>
				<div class="list-item-description"><?= $item['description']?></div>
				<div class="list-item-footer icon-wrap margin-t-5">
					<span><i class="glyphicon glyphicon-th-list" aria-hidden="true"></i><a href="/category-<?=$item['category_id']?>.html"><?= $userCache->getArticleCategoryNameById($item['category_id'])?></a></span>
					<span><i class="glyphicon glyphicon-time" aria-hidden="true"></i><?= date('Y-m-d', $item['created_at'])?></span>
					<span><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>阅读(<?= $item['hits']?>)</span>
					<span><i class="glyphicon glyphicon-comment" aria-hidden="true"></i>评论(<?= $item['comment_count']?>)</span>
				</div>
				<div class="list-item-separator"></div>
			</div>
		<?php endforeach;?>
	<?php else :?>
		<div>
			<p class="bg-danger padding-15">没有找到数据哦 !</p>
		</div>
	<?php endif;?>
	<nav aria-label="Page navigation">
		<?= yii\widgets\LinkPager::widget([
			'pagination' => $pages,
			'maxButtonCount' => 5,
			'nextPageLabel' => '下一页', // 修改上下页按钮
			'prevPageLabel' => '上一页',
		]) ?>
	</nav>
</div>
