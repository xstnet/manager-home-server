<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%home}}".
 *
 * @property int $id
 * @property string $name home name
 * @property int $creator 创建人ID， 关联user表
 * @property int $master_user_id 家主，可以管理家
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Home extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%home}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['creator', 'master_user_id', 'created_at', 'updated_at'], 'integer'],
            [['name',], 'filter', 'filter' => function ($value) {
                return self::filterStr($value);
            }],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'home name',
            'creator' => '创建人ID， 关联user表',
            'master_user_id' => '家主，可以管理家',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
