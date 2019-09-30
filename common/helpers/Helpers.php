<?php
/**
 * Desc: array 助手
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/6/27
 * Time: 11:00
 */

namespace common\helpers;

use Yii;

class Helpers
{
	/**
	 * @Desc: 获取分类树
	 * @param array $list 列表数据 id => data
	 * @param string $parentName
	 * @param string $childName
	 * @return array
	 */
	public static function getTree($list, $parentName = 'parent_id', $childName = 'children')
	{
		$tree = []; //格式化好的树  id => array
		foreach ($list as $item) {
			// 不是一级分类，父级分类不存在，跳过
			if ($item[ $parentName ] != 0 && !isset($list[ $item[ $parentName ] ])) {
				continue;
			}
			if (isset($list[$item[ $parentName ]])) {
				$list[$item[ $parentName ]][ $childName ][] = &$list[$item['id']];
			} else {
				$tree[] = &$list[$item['id']];
			}
		}

		return $tree;
	}

	/**
	 * 渲染面包屑导航
	 * @param $breadcrumb
	 * @return string
	 */

    public static function getHomeId()
    {
        $defaultHomeId = (int) Yii::$app->request->get('default_home_id');
        $homeId = (int) Yii::$app->request->get('homeId');
        if ($homeId <= 0) {
            $homeId = (int) Yii::$app->request->post('homeId');
        }
        if ($homeId > 0) {
            return $homeId;
        }

        return $defaultHomeId;
    }
}