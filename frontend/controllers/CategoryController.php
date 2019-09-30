<?php
/**
 * Created by PhpStorm
 * Author: Shantong Xu <shantongxu@qq.com>
 * Date: 2019/9/26
 * Time: 10:48 下午
 */

namespace frontend\controllers;


use frontend\services\category\CategoryService;
use yii\filters\VerbFilter;

class CategoryController extends BaseController
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'get-category-list' => ['get', ],
                        'create-category' => ['post', ],
                        'create-tag' => ['post', ],
                        'delete-tag' => ['post', ],
                        'update-tag' => ['post', ],
                        'update-category' => ['post', ],
                        'delete-category' => ['post', ],
                    ],
                ],
            ]
        );
    }

    public function actions()
    {
        return [];
    }


    /**
     * 获取类目列表
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetCategoryList()
    {
        $result = CategoryService::getInstance()->getCategoryList();
        return static::returnJson($result);
    }

    /**
     * 添加分类
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreateCategory()
    {
        $params = static::postParams();
        $result = CategoryService::getInstance()->createCategory($params);

        return static::returnJson('添加成功', $result);
    }

    /**
     * 添加标签
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreateTag()
    {
        $params = static::postParams();
        $result = CategoryService::getInstance()->createTag($params);

        return static::returnJson('添加成功', $result);
    }

    /**
     * 添加家具
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreateFurniture()
    {
        $params = static::postParams();
        $result = CategoryService::getInstance()->createFurniture($params);

        return static::returnJson('添加成功', $result);
    }

}