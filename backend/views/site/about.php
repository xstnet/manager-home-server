<?php

/* @var $this yii\web\View */
/* @var $article \common\models\Article */
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
	<div>
		<?=$article->content->content?>
	</div>
	<br>
	<br>
	<br>
	
	<div class="message">
		<h2>给我留言</h2>
		<hr class="hr">
		<form action="/message/release.html" method="post">
			<input type="hidden" name="_csrf-avwd" value="<?=Yii::$app->request->csrfToken?>" />
			<input type="hidden" name="from" value="about" />
			<div class="form-group">
				<label for="exampleInputEmail1">尊姓大名</label>
				<input type="text" required class="form-control" name="nickname" placeholder="您的尊姓大名">
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">Email</label>
				<input type="email" class="form-control" name="email" placeholder="Email(可填)">
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">内容(不超过255个字符)</label>
				<textarea required class="form-control" name="content" placeholder="说点什么吧" rows="8"></textarea>
			</div>

			<button type="submit" class="btn btn-default">提交</button>
		</form>
		<br>
		<br>
		<h2>最新留言</h2>
		<hr class="hr">
		<!--		<img width="70" src="/uploads/avatar/1.jpg" class="img-circle">-->

		<div>
			<ul class="media-list">
				<?php foreach ($messageList as $item) :?>
					<li class="media">
						<div class="media-left">
							<a href="#">
								<img width="64" class="media-object" src="<?=$item['avatar']?>">
							</a>
						</div>
						<div class="media-body">
							<h4 class="media-heading text-primary"><?=Html::encode($item['nickname']) . (!empty($item['email']) ? "(" .Html::encode($item['email']).")" : '')?></h4>
							<div class="message-content" style="margin-bottom: 5px"><?=Html::encode($item['content'])?></div>
							<p class="text-muted margin-0">发布于: <?=date('Y-m-d H:i', $item['created_at'])?></p>
						</div>
						<hr class="hr">
					</li>
				<?php endforeach;?>
			</ul>
			<nav aria-label="Page navigation">
				<?= yii\widgets\LinkPager::widget([
					'pagination' => $pages,
					'maxButtonCount' => 5,
					'nextPageLabel' => '下一页', // 修改上下页按钮
					'prevPageLabel' => '上一页',
				]) ?>
			</nav>
		</div>
	</div>
</div>
