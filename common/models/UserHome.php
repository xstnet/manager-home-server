<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_home}}".
 *
 * @property int $id
 * @property int $user_id 用户ID 
 * @property int $home_id
 * @property int $is_default 是否默认，1:默认，0:不是默认
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class UserHome extends \common\models\BaseModel
{
    const IS_DEFAULT_YES = 1;
    const IS_DEFAULT_NO = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_home}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['home_id'], 'required'],
            [['user_id', 'home_id', 'is_default', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID ',
            'home_id' => 'Home ID',
            'is_default' => '是否默认，1:默认，0:不是默认',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
