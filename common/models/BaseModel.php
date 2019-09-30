<?php
/**
 * Created by PhpStorm.
 * Desc: Model 的基类
 * User: xstnet.com
 * Date: 2017/10/17
 * Time: 22:51
 */

namespace common\models;


use common\exceptions\DatabaseException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    const SCENARIO_INSERT = 'insert'; // 新增场景
    const SCENARIO_UPDATE = 'update'; // 更新场景

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static $defaultSearchField = [
        'name' => 'name',
        'field' => '',
        'type' => 'text',
        'condition' => '=',
        'format' => ''
    ];

    public static function searchFields()
    {
        return [];
    }

    public static function mapSearchFields()
    {
        return [];
    }

    public static function getSearchFieldsByKey(string $key = '')
    {
        if (!isset(static::mapSearchFields()[$key])) {
            return [];
        }
        $searchFieldKeyList = static::mapSearchFields()[$key];
        $result = [];
        $searchFields = static::searchFields();
        foreach ($searchFieldKeyList as $item) {
            if (isset($searchFields[$item])) {
                $result[$item] = array_merge(static::$defaultSearchField, $searchFields[$item]);
            }
        }

        return $result;
    }

    /**
     * @Desc 公共save
     * @param bool|object $transaction \yii\db\Transaction
     * @param bool|object $validate
     * @throws DatabaseException
     */
    public function saveModel($transaction = false, $validate = true)
    {
        if(!$this->save($validate)){
            if ($transaction) {
                $transaction->rollBack();
            }
            $type = DatabaseException::UPDATE_ERROR;
            if($this->isNewRecord){
                $type = DatabaseException::INSERT_ERROR;
            }
            $errors = $this->getFirstErrors();
            $error = reset($errors);
            Yii::error($error);
            throw new DatabaseException($type, $error);
        }
    }
    /**
     * @Desc 公共delete
     * @param bool|object $transaction \yii\db\Transaction
     * @throws DatabaseException
     */
    public function deleteModel($transaction = false)
    {
        if(!$this->delete()){
            if ($transaction) {
                $transaction->rollBack();
            }
            $type = DatabaseException::UPDATE_ERROR;
            $errors = $this->getFirstErrors();
            $error = reset($errors);
            Yii::error($error);
            throw new DatabaseException($type, $error);
        }
    }

    /**
     * 过滤空格
     * ' " <> 转化为 html实体
     * @param string $str 原字符串
     * @return string
     */
    public static function filterStr($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, Yii::$app ? Yii::$app->charset : 'UTF-8', true);
    }

    public function formName()
    {
//		return parent::formName();
        return '';
    }
}