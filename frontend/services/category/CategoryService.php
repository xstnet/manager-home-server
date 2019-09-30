<?php
/**
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/10/27
 * Time: 13:48
 */

namespace frontend\services\category;


use common\exceptions\ParameterException;
use common\helpers\Helpers;
use common\models\Category;
use common\models\CategoryTag;
use common\models\HomeFurniture;
use common\models\HomeRoom;
use frontend\services\BaseService;
use Yii;

class CategoryService extends BaseService implements CategoryServiceInterface
{

    /**
     * 获取类目和标签列表
     * @return array
     */
    public function getCategoryList()
    {
        $homeId = Helpers::getHomeId();
        $categoryListQueryRet = Category::find()
            ->alias('c')
            ->select(Category::getListFields())
            ->leftJoin(['t' => CategoryTag::tableName()], 'c.id = t.category_id')
            ->where(['home_id' => $homeId])
            ->orderBy([
                'c.created_at' => SORT_DESC,
                't.created_at' => SORT_DESC,
            ])
            ->createCommand()
            ->queryAll();

        $categoryList = [];
        foreach ($categoryListQueryRet as $item) {
            if (!isset($categoryList[$item['id']])) {
                $categoryList[$item['id']] = [
                    'name' => $item['name'],
                    'id' => $item['id'],
                    'articleCount' => $item['articleCount'],
                    'tagCount' => $item['tagCount'],
                    'tagList' => [],
                ];
            }
            if (!empty($item['tagId'])) {
                $categoryList[$item['id']]['tagList'][] = [
                    'id' => $item['tagId'],
                    'name' => $item['tagName'],
                    'articleCount' => $item['tagArticleCount'],
                ];
            }
        }

        return ['categoryList' => array_values($categoryList)];
    }

    /**
     * 添加类目
     * @param $params
     * @return array
     * @throws \yii\db\Exception
     */
    public function createCategory($params)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $tagListArr = explode(',', trim($params['tagList']));

            $category = new Category();
            $category->home_id = Helpers::getHomeId();
            $category->name = trim($params['name']);
            $category->tag_count = count($tagListArr);
            $category->comment = trim($params['comment']);
            $category->saveModel($transaction);

            if (!empty($tagListArr)) {
                foreach ($tagListArr as $tag) {
                    $tagModel = new CategoryTag();
                    $tagModel->name = $tag;
                    $tagModel->category_id = $category->id;
                    $tagModel->saveModel($transaction);
                }
            }

            $result = [
                'id' => $category->id,
                'name' => $category->name,
                'articleCount' => 0,
                'tagList' => CategoryTag::find()->select(CategoryTag::getListField())->asArray()->all(),
            ];


            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return ['category' => $result];
    }

    public function createTag($params)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $categoryId = (int) ($params['categoryId'] ?? 0);
            $category = Category::findOne($categoryId);
            if (empty($category)) {
                throw new ParameterException(ParameterException::INVALID, '类目不存在!');
            }

            $tag = new CategoryTag();
            $tag->name = $params['tagName'] ?? '';
            $tag->category_id = $category->id;
            $tag->saveModel($transaction);

            $category->tag_count ++;
            $category->saveModel($transaction);

            $result = [
                'id' => (int) $tag->id,
                'tagName' => $tag->name,
                'categoryId' => (int) $category->id,
                'articleCount' => 0,
            ];


            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * @Desc: 获取家具分类树
     * @param array $list 列表数据 id => data
     * @param string $parentName
     * @param string $childName
     * @return array
     */
    private static function getFurnitureTree($list, $parentName = 'parent_id', $childName = 'children')
    {
        $tree = []; //格式化好的树  id => array
        foreach ($list as $item) {
            // 不是一级分类，父级分类不存在，跳过
            if ($item[ $parentName ] != 0 && !isset($list[ $item[ $parentName ] ])) {
                continue;
            }
            $list[$item['id']]['value'] = (int) $list[$item['id']]['value'];
            $list[$item['id']]['id'] = (int) $list[$item['id']]['id'];
            if (isset($list[$item[ $parentName ]])) {
                if (!isset($list[$item[ $parentName ]][ $childName ])) {
                    $list[$item[ $parentName ]][ $childName ][] = ['label' => '不选', 'value' => '不选'];
                }
                $list[$item[ $parentName ]][ $childName ][] = &$list[$item['id']];
            } else {
                $tree[] = &$list[$item['id']];
            }
        }

        return $tree;
    }

}