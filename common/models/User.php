<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $nickname
 * @property string $password
 * @property string $mobile
 * @property int $total_upload_image_number
 * @property int $uploaded_image_number
 * @property string $register_ip
 * @property string $login_ip
 * @property string $last_login_ip
 * @property integer $login_at
 * @property integer $last_login_at
 * @property integer $login_count
 * @property integer $status
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class User extends BaseModel implements \yii\web\IdentityInterface
{
    const STATUS_DISABLED = 10;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'total_upload_image_number', 'uploaded_image_number', 'login_at', 'last_login_at', 'login_count', 'created_at', 'updated_at'], 'integer'],
            [['username', 'nickname', 'mobile'], 'required', 'message' => '{attribute}不能为空'],
            [['username', 'nickname', ], 'string', 'max' => 15, 'min' => 2],
            ['mobile', 'string', 'length' => 11,],
            [['password'], 'string', 'max' => 64],
            [['register_ip', 'login_ip', 'last_login_ip'], 'string', 'max' => 16],
            [['username'], 'unique', 'message' => '该账号已存在'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '账号',
            'nickname' => 'Nickname',
            'password' => 'Password',
            'status' => '状态，1：正常， 10：禁用',
            'mobile' => '手机号',
            'total_upload_image_number' => '总可用上传图片数量',
            'uploaded_image_number' => '已上传图片数量',
            'register_ip' => '注册IP',
            'login_ip' => '本次登录IP',
            'last_login_ip' => '上次登录IP',
            'login_at' => '本次登录时间',
            'last_login_at' => '上一次登录时间',
            'login_count' => '登录统计',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::findOne(['id' => $token]);
        if (empty($user)) {
            throw new DatabaseException(DatabaseException::UNKNOWN, '账号不存在');
        }
        if ($user->status == static::STATUS_DISABLED) {
            throw new DatabaseException(DatabaseException::UNKNOWN, '账号已被禁用');
        }

        return $user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password, 5);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
//		$this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

}
