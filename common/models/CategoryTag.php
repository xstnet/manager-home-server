<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category_tag}}".
 *
 * @property int $id
 * @property int $category_id 所属类目ID 
 * @property string $name 名称
 * @property int $index 排序值，升序
 * @property int $article_count 物品数量
 * @property string $comment 备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class CategoryTag extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'index', 'article_count', 'created_at', 'updated_at'], 'integer'],
            [['name', 'comment'], 'filter', 'filter' => function ($value) {
                return self::filterStr($value);
            }],
            [['name'], 'string', 'max' => 15],
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
            'category_id' => '所属类目ID ',
            'name' => '名称',
            'index' => '排序值，升序',
            'article_count' => '物品数量',
            'comment' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getListField()
    {
        return [
            'id', 'categoryId' => 'category_id',
            'articleCount' => 'article_count', 'name',
        ];
    }
}
