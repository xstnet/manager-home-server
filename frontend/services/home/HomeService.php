<?php
/**
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/10/27
 * Time: 13:48
 */

namespace frontend\services\home;


use common\helpers\Helpers;
use common\models\HomeFurniture;
use common\models\HomeRoom;
use frontend\services\BaseService;
use Yii;

class HomeService extends BaseService implements HomeServiceInterface
{

    /**
     * 获取房间列表
     * @return array
     */
    public function getRoomList()
    {
        $homeId = Helpers::getHomeId();
        $roomListQueryRet = HomeRoom::find()
            ->select(HomeRoom::getListFields())
            ->where(['home_id' => $homeId])
            ->asArray()
            ->all();
        $roomList = [];
        foreach ($roomListQueryRet as $v) {
            $v['furnitureList'] = [];
            $v['id'] = (int) $v['id'];
            $roomList[$v['id']] = $v;
        }

        /**
         * 组装家具数据
         */
        $furnitureList = HomeFurniture::find()
            ->select(HomeFurniture::getTreeListField())
            ->where(['home_id' => $homeId])
            ->indexBy('id')
            ->asArray()
            ->all();

        $furnitureTree = self::getFurnitureTree($furnitureList);
        foreach ($furnitureTree as $item) {
            if ($item['label'] === '不选') {
                continue;
            }
            if (isset($roomList[$item['room_id']])) {
                $roomList[$item['room_id']]['furnitureList'][] = $item;
            }
        }

        return ['roomList' => array_values($roomList)];
    }

    /**
     * 添加房间
     * @param $params
     * @return array
     * @throws \yii\db\Exception
     */
    public function createRoom($params)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $room = new HomeRoom();
            $room->home_id = Helpers::getHomeId();
            $room->name = $params['name'];
            $room->comment = $params['comment'];
            $room->font_icon = $params['fontIcon'];
            $room->saveModel($transaction);

            $result = [
                'id' => $room->id,
                'name' => $room->name,
                'icon' => $room->font_icon,
                'furnitureList' => [],
            ];


            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return ['room' => $result];
    }

    /**
     * 添加家具
     * @param $params
     * @return array
     * @throws \yii\db\Exception
     */
    public function createFurniture($params)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $parentArray = explode(',', trim($params['parentIds']));
            $furniture = new HomeFurniture();
            $furniture->home_id = Helpers::getHomeId();
            $furniture->room_id = $params['roomId'];
            $furniture->name = trim($params['name']);
            $furniture->comment = trim($params['comment']);
            $furniture->parents = trim($params['parentIds']);
            $furniture->parent_id = (int) ($parentArray[count($parentArray) - 1]);
            $furniture->saveModel($transaction);

            // 更新父元素包含的子家具数量
            if ($furniture->parent_id != 0) {
                HomeFurniture::updateAllCounters(['furniture_count' => 1], ['id' => $furniture->parent_id]);
            } else {
                HomeRoom::updateAllCounters(['furniture_count' => 1], ['id' => $furniture->room_id]);
            }
            if ($furniture->parent_id == 0) {
                $furniture->parents = (string) $furniture->id;
            } else {
                $furniture->parents .= ',' . $furniture->id;
            }
            $furniture->saveModel($transaction);

            $result = [
                'id' => (int) $furniture->id,
                'name' => $furniture->name,
                'roomId' => (int) $furniture->room_id,
                'articleCount' => 0,
                'subFurnitureCount' => 0,
                'parentIds' => $furniture->parents,
            ];

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return ['furniture' => $result];
    }

    /**
     * @Desc: 获取家具分类树
     * @param array $list 列表数据 id => data
     * @param string $parentName
     * @param string $childName
     * @return array
     */
    private static function getFurnitureTree($list, $parentName = 'parent_id', $childName = 'children')
    {
        $tree = []; //格式化好的树  id => array
        foreach ($list as $item) {
            // 不是一级分类，父级分类不存在，跳过
            if ($item[ $parentName ] != 0 && !isset($list[ $item[ $parentName ] ])) {
                continue;
            }
            $list[$item['id']]['value'] = (int) $list[$item['id']]['value'];
            $list[$item['id']]['id'] = (int) $list[$item['id']]['id'];
            if (isset($list[$item[ $parentName ]])) {
                if (!isset($list[$item[ $parentName ]][ $childName ])) {
                    $list[$item[ $parentName ]][ $childName ][] = ['label' => '不选', 'value' => '不选'];
                }
                $list[$item[ $parentName ]][ $childName ][] = &$list[$item['id']];
            } else {
                $tree[] = &$list[$item['id']];
            }
        }

        return $tree;
    }

}