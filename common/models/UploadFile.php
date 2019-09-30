<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%upload_files}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property integer $size
 * @property string $extend
 * @property string $md5
 * @property string $mime_type
 * @property integer $created_at
 * @property integer $updated_at
 */
class UploadFile extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%upload_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 150],
            [['mime_type'], 'string', 'max' => 50],
            [['extend'], 'string', 'max' => 20],
            [['md5'], 'string', 'max' => 32],
            [['md5'], 'unique'],
        ];
    }
}
