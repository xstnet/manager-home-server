<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%article_tag}}".
 *
 * @property int $id
 * @property int $article_id
 * @property int $tag_id
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ArticleTag extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%article_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'tag_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'tag_id' => 'Tag ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
