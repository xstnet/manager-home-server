<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

if (!isset($searchFields) || empty($searchFields)) {
	return '';
}

?>

<form class="layui-form1" action="" id="searchForm">
	<div class="search-fields-wrap">
		<?php
		foreach ($searchFields as $key => $field) :
			$myClass = 'my-input-' . $field['type'];
			if ($field['type'] == 'date') {
				$field['type'] = 'text';
			}
		?>
		<div class="layui-input-block" style="float: left; position: relative;">
			<label class="layui-form-label"><?=$field['name']?>: </label>
			<?php if ($field['type'] != 'select') :?>
			<input style="width: auto;" type="<?=$field['type']?>" name="<?=$key?>" placeholder="请输入<?=$field['name']?>" autocomplete="off" class="layui-input <?=$myClass?>">
			<?php else:?>
				<select style="width: auto" class="layui-input" name="<?=$key?>">
					<option value="">请选择<?=$field['name']?></option>
					<?php
						$selectKeyParam = 'select_' . $key;
						echo $$selectKeyParam;
					?>
				</select>
			<?php endif;?>
		</div>
		
		<?php endforeach;?>
		<button style="margin-top: 5px; margin-left: 10px" class="layui-btn layui-btn-sm" lay-filter="form-search-form" lay-submit="" id="searchBtn" data-type="getInfo" style="float: left;">搜索</button>
		<button style="margin-top: 5px; margin-left: 5px" class="layui-btn layui-btn-primary layui-btn-sm" type="reset" id="searchBtnReset" data-type="getInfo" style="float: left;">重置</button>
	</div>
</form>