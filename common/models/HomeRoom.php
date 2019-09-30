<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%home_room}}".
 *
 * @property int $id
 * @property int $home_id
 * @property string $name 名称
 * @property int $article_count 物品数量
 * @property int $furniture_count 家具数量
 * @property string $font_icon 图标
 * @property int $index 排序值，升序
 * @property string $comment 备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class HomeRoom extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%home_room}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'article_count', 'furniture_count', 'index', 'created_at', 'updated_at'], 'integer'],
            [['name', 'comment'], 'filter', 'filter' => function ($value) {
                return self::filterStr($value);
            }],
            [['name', 'font_icon'], 'string', 'max' => 20],
            [['comment'], 'string', 'max' => 255],
            [['article_count', 'furniture_count'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'home_id' => 'Home ID',
            'name' => '名称',
            'article_count' => '物品数量',
            'furniture_count' => '家具数量',
            'font_icon' => '图标',
            'index' => '排序值，升序',
            'comment' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getListFields()
    {
        return [
            'id', 'home_id', 'name', 'icon' => 'font_icon',
        ];
    }

    public static function getNameById($homeId, $id)
    {
        static $roomList = [];
        if (empty($roomList)) {
            $roomList = HomeRoom::find()->where(['home_id' => $homeId])->indexBy('id')->asArray()->all();
        }

        if (isset($roomList[$id])) {
            return $roomList[$id]['name'];
        }

        return '';
    }

}
