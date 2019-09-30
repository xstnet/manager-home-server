<?php

/* @var $this yii\web\View */
/* @var $article \common\models\Article */
use yii\helpers\Html;

$deleteText = '<span style="color: #FF5722">该评论已被删除!</span>'
//?>
<ul class="media-list">
	<?php if (!empty($commentList)) :?>
	<?php foreach ($commentList as $item) :?>
		<li class="media">
			<div class="media-left">
				<a href="#">
					<img width="64" class="media-object" src="<?=$item['avatar']?>">
				</a>
			</div>
			<div class="media-body">
				<h4 class="media-heading text-primary"><?=Html::encode($item['nickname']) . (!empty($item['email']) ? "(" .Html::encode($item['email']).")" : '')?></h4>
				<div class="message-content" style="margin-bottom: 5px"><?=$item['is_delete'] == 1 ? $deleteText : Html::encode($item['content'])?></div>
				<p class="text-muted margin-0">发布于: <?=date('Y-m-d H:i', $item['created_at'])?></p>
			</div>
			<hr class="hr">
		</li>
	<?php endforeach;?>
	<?php else:?>
	<p>暂无评论, 快来抢沙发吧</p>
	<?php endif;?>
</ul>
<nav aria-label="Page navigation">
	<?= yii\widgets\LinkPager::widget([
		'pagination' => $pages,
		'maxButtonCount' => 5,
		'nextPageLabel' => '下一页', // 修改上下页按钮
		'prevPageLabel' => '上一页',
	]) ?>
</nav>


