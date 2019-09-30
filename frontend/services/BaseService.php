<?php
/**
 * Desc: 公共service
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/9/9
 * Time: 21:23
 */

namespace frontend\services;
use common\exceptions\ParameterException;
use common\models\form\UploadForm;
use Yii;
use yii\web\UploadedFile;


class BaseService
{
	protected static $_instance = [];

	public static $defaultPageSie = 20;

	/**
	 * @Desc: 创建实例
	 * @param array $params
	 * @return static
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getInstance($params = [])
	{
		$className = get_called_class();
		if (!isset(static::$_instance[ $className] )) {
			$params['class'] = $className;
			static::$_instance[ $className ] = \Yii::createObject($params);
		}

		return static::$_instance[ $className ];
	}

	/**
	 * @Desc: 获取分页
	 * @param $query \yii\db\ActiveQuery
	 * @param array $searchFields
	 * @return array
	 */
	public static function getPageAndSearch($query, $searchFields = [])
	{
		$defaultPageSie = self::$defaultPageSie;
		$page = (int) Yii::$app->request->get('page', 1);
		$pageSize = (int) max(Yii::$app->request->get('limit', self::$defaultPageSie), Yii::$app->request->get('pageSize', $defaultPageSie));

		$page = $page < 1 ? 1 : $page;
		$pageSize = $pageSize < 1 ? $defaultPageSie : $pageSize;
		
		if (!empty($searchFields)) {
			$where = self::buildSearchFields($searchFields);
			$query->andWhere($where);
		}

		$count = $query->count();

		$offset = ($page - 1) * $pageSize;

		$query->offset($offset)->limit($pageSize);

        $more = 1;
        if ($page * $pageSize >= $count || $pageSize < 0) {
            $more = 0;
        }

		return [$count, $page, $more ];
	}
	
	public static function buildSearchFields(array $searchFields = []) : array
	{
		$get = Yii::$app->request->get();
		$where = [];
		foreach ($searchFields as $name => $item) {
			if (isset($get[$name]) && !empty($get[$name])) {
				$value = $get[$name];
				$field = $name;
				if (!empty($item['field'])) {
					$field = $item['field'];
				}
				if ($item['type'] == 'date' || $item['type'] == 'datetime') {
					$value = strtotime($value);
					if (empty($value)) {
						continue;
					}
				}
				if (!empty($field['format'])) {
					$value = call_user_func($item['format'], $value);
//					$value = $item['format']($value);
				}
				$where[] = [$item['condition'], $field, $value];
			}
		}
		if (!empty($where)) {
			array_unshift($where, 'and');
		}
		
		return $where;
	}

    /**
     * 上传图片文件
     * @param string $name
     * @return array
     * @throws ParameterException
     */
	public static function uploadImages($name = '')
    {
        $model = new UploadForm();
        $model->imageFiles = UploadedFile::getInstances($model, $name);
        $model->scenario =  UploadForm::SCENARIO_IMAGE_FILE;
        $uploadResult = $model->uploadImageFiles();
        if ($uploadResult) {
            // 文件上传成功
            return $uploadResult;
        } else {
            $error = $model->getErrors()['imageFile'][0];
            throw new ParameterException(ParameterException::INVALID, $error);
        }
    }

    /**
     * 上传图片文件
     * @param string $name
     * @return array
     * @throws ParameterException
     */
    public static function uploadImage($name = '')
    {
        $model = new UploadForm();
        $result = [];
        $model->imageFile = UploadedFile::getInstance($model, $name);
        $model->scenario =  UploadForm::SCENARIO_IMAGE_FILE;
        $uploadResult = $model->uploadImageFile();
        if ($uploadResult) {
            // 文件上传成功
            return $uploadResult;
        } else {
            $error = $model->getErrors()['imageFile'][0];
            throw new ParameterException(ParameterException::INVALID, $error);
        }
    }
}