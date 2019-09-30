<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property int $id
 * @property int $home_id 所属家
 * @property int $room_id 所属房间
 * @property int $category_id 类目ID
 * @property int $furniture_id 直接房间ID
 * @property string $furniture_ids 上级房间
 * @property string $name 名称
 * @property int $quantity 数量
 * @property string $logo logo
 * @property string $price 价格
 * @property int $buy_at 购买时间
 * @property string $color 颜色
 * @property string $own_user 拥有人
 * @property int $creator 创建人
 * @property string $comment 备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Article extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'room_id', 'category_id', 'furniture_id', 'quantity', 'buy_at', 'creator', 'created_at', 'updated_at'], 'integer', 'message' => '{attribute}必须为数字'],
            [['price'], 'number'],
            [['name', 'comment'], 'filter', 'filter' => function ($value) {
                return self::filterStr($value);
            }],
            [['furniture_ids'], 'string', 'max' => 60],
            [['name', 'own_user'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 200],
            [['color'], 'string', 'max' => 10],
            [['comment'], 'string', 'max' => 255],
            ['quantity', 'integer', 'min' => 1, 'max' => 65535, 'tooBig' => '数量不能超过65535', 'tooSmall' => '数量不能小于1'],
            [['name', 'room_id', 'category_id', 'home_id'], 'required', 'message' => '{attribute}不能为空' ],
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
            'category_id' => '类目ID',
            'furniture_id' => '直接房间ID',
            'furniture_ids' => '上级房间',
            'name' => '名称',
            'quantity' => '数量',
            'logo' => 'logo',
            'price' => '价格',
            'buy_at' => '购买时间',
            'color' => '颜色',
            'own_user' => '拥有人',
            'creator' => '创建人',
            'comment' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getListField()
    {
        return [
            'a.id', 'a.name', 'a.logo', 'a.comment',
            'a.room_id',
            'location' => 'a.furniture_ids'
        ];
    }

    public static function searchFields()
    {
        return [
            'title' => [
                'name' => '标题',
                'field' => 'title',
                'width' => 0,
                'type' => 'text',
                'condition' => 'like',
                'format' => ''
            ],
            'start_at' => [
                'name' => '开始时间',
                'field' => 'article.created_at',
                'type' => 'date',
                'condition' => '>=',
            ],
            'end_at' => [
                'name' => '结束时间',
                'field' => 'article.created_at',
                'type' => 'date',
                'condition' => '<',
            ],
            'id' => [
                'field' => 'article.id',
                'name' => 'ID',
                'type' => 'number',
            ],
        ];
    }

    public static function mapSearchFields()
    {
        $searchFieldKeyList = [
            'index' => [
                'id', 'title', 'category_id', 'start_at', 'end_at',
            ],
        ];
        return $searchFieldKeyList;
    }
}
