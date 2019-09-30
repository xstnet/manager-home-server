<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property string $name 类目名称
 * @property int $home_id 所属家ID 
 * @property int $index 排序值，升序
 * @property int $tag_count 标签
 * @property int $article_count 物品数量
 * @property string $comment 备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Category extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'index', 'article_count', 'tag_count', 'created_at', 'updated_at'], 'integer'],
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
            'name' => '类目名称',
            'home_id' => '所属家ID ',
            'index' => '排序值，升序',
            'tag_count' => '标签数量',
            'article_count' => '物品数量',
            'comment' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getListFields()
    {
        return [
            'c.id', 'c.name', 'articleCount' => 'c.article_count',
            'tagArticleCount' => 't.article_count',
            'tagCount' => 'tag_count',
            'tagName' => 't.name',
            'tagId' => 't.id',
        ];
    }

}
