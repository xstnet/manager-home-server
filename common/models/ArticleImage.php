<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%article_image}}".
 *
 * @property int $id
 * @property int $home_id
 * @property int $article_id
 * @property string $url
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ArticleImage extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%article_image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'article_id', 'created_at', 'updated_at'], 'integer'],
            [['url'], 'required'],
            [['url'], 'string', 'max' => 200],
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
            'article_id' => 'Article ID',
            'url' => 'Url',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
