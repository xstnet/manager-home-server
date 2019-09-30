<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%home_member}}".
 *
 * @property int $id
 * @property int $home_id
 * @property int $user_id
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class HomeMember extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%home_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
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
            'user_id' => 'User ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
