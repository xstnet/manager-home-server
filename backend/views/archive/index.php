<?php

/* @var $this yii\web\View */
/* @var $article \common\models\Article */
use yii\helpers\Html;

$this->title =  '归档';
$this->params['active_menu'] = 'archive';

$userCache = Yii::$app->userCache;
//?>

<div class="col-md-9 content-left">
	<div class="archive">
		<?php
		foreach ($archiveList as $year => $item) :
		?>
		<h2><?=$year?></h2>
		<ul>
			<?php
			foreach ($item as $value) :
				list ($year, $month) = explode('-', $value['date']);
			?>
				<li><a href="/archive/<?=$year.'/'.$month?>.html"><?=$year . '年' . $month . '月'?>(<?=$value['count']?>)</a></li>
			<?php endforeach;?>
			
		</ul>
		<?php endforeach;?>
	</div>
	
</div>