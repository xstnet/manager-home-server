<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%home_furniture}}".
 *
 * @property int $id
 * @property int $home_id
 * @property int $room_id
 * @property string $name 名称
 * @property int $parent_id 所属的家具
 * @property string $parents 所有上级家具
 * @property int $article_count 物品数量
 * @property int $furniture_count 子家具数量
 * @property string $comment 备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class HomeFurniture extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%home_furniture}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'room_id', 'parent_id', 'article_count', 'furniture_count', 'created_at', 'updated_at'], 'integer'],
            [['name', 'comment'], 'filter', 'filter' => function ($value) {
                return self::filterStr($value);
            }],
            [['name'], 'string', 'max' => 30],
            [['parents'], 'string', 'max' => 60],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'home_id' => '所属家',
            'room_id' => '所属房间',
            'name' => '名称',
            'parent_id' => '所属的家具',
            'parents' => '所有上级家具',
            'article_count' => '物品数量',
            'furniture_count' => '子家具数量',
            'comment' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getTreeListField()
    {
        return [
            'id',
            'value' => 'id',
            'label' => 'name',
            'articleCount' => 'article_count',
            'subFurnitureCount' => 'furniture_count',
            'parent_id', 'room_id'
        ];
    }

    public static function getNames($homeId, $ids)
    {
        $result = [];
        static $list = [];
        if (empty($list)) {
            $list = self::find()
                ->where(['home_id' => $homeId])
                ->asArray()
                ->indexBy('id')
                ->all();
        }

        foreach (explode(',', $ids) as $id) {
            if (isset($list[$id])) {
                $result[] = $list[$id]['name'];
            }
        }
        return join(' > ', $result);
    }

}
