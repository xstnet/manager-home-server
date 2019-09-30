<?php
/**
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/10/27
 * Time: 13:48
 */

namespace frontend\services\article;


use common\exceptions\ParameterException;
use common\helpers\Helpers;
use common\models\Article;
use common\models\ArticleImage;
use common\models\ArticleTag;
use common\models\Category;
use common\models\CategoryTag;
use common\models\Home;
use common\models\HomeFurniture;
use common\models\HomeRoom;
use frontend\services\BaseService;
use Yii;

class ArticleService extends BaseService implements ArticleServiceInterface
{

    /**
     * 获取类目和标签列表
     * @return array
     */
    public function getArticleList()
    {
        $homeId = Helpers::getHomeId();

        try {
            $home = Home::findOne($homeId);
            if (empty($home)) {
                throw new ParameterException(1, '不存在的家！');
            }

            $query = $articleListRet = Article::find()
                ->alias('a')
                ->select(Article::getListField())
                ->where(['home_id' => $homeId]);

            list ($count, $page, $more) = self::getPageAndSearch($query);

            $articleListRet = $query
                ->orderBy(['a.created_at' => SORT_DESC])
                ->createCommand()
                ->queryAll();


            foreach ($articleListRet as $key => $item) {
                $articleListRet[$key]['location'] = HomeRoom::getNameById($homeId, $item['room_id']) . ' > ' . HomeFurniture::getNames($homeId, $item['location']);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $result = [
            'list' => $articleListRet,
            'more' => $more,
            'page' => $page,
            'count' => $count,
        ];
    }

    /**
     * 添加物品
     * @param $params
     * @return array
     * @throws \yii\db\Exception
     */
    public function createArticle($params)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $tagList = (array) ($params['tags'] ?? []);
            foreach ($tagList as $key => $vo) {
                $tagList[$key] = (int) $vo;
            }

            $ownUser = (array) ($params['ownUser'] ?? []);
            foreach ($ownUser as $key => $vo) {
                $ownUser[$key] = (int) $vo;
            }

            $furnitureList = explode(',', ($params['furnitureIds'] ?? ''));
            foreach ($furnitureList as $key => $vo) {
                $furnitureList[$key] = (int) $vo;
            }

            $article = new Article();
            $article->name = $params['name'] ?? '';
            $article->category_id = $params['categoryId'] ?? 0;
            $article->price = $params['price'] ?? 0;
            $article->color = $params['color'] ?? '';
            $article->room_id = $params['roomId'] ?? 0;
            $article->furniture_id = empty($furnitureList) ? 0 : $furnitureList[count($furnitureList) - 1];
            $article->furniture_ids = join(',', $furnitureList);
            $article->home_id = Helpers::getHomeId();
            $article->creator = (int) Yii::$app->user->id;
            $article->buy_at = (int) strtotime($params['buyDate']);
            $article->own_user = join(',', $ownUser);
            $article->comment = $params['comment'] ?? '';
            $article->quantity = $params['quantity'] ?? 1;
            if (!$article->validate()) {
                $error = current($article->getFirstErrors());
                throw new ParameterException(ParameterException::INVALID, $error);
            }

            // 上传文件
            $uploadImages = self::uploadImages('imageFile');
            $article->logo = empty($uploadImages) ? '' : $uploadImages[0];
            $article->saveModel($transaction, false);

            /*
             * 添加标签
             */
            foreach ($tagList as $tagId) {
                $articleTag = new ArticleTag();
                $articleTag->article_id = $article->id;
                $articleTag->tag_id = $tagId;
                $articleTag->saveModel($transaction);
            }

            /*
             * 保存图片
             */
            foreach ($uploadImages as $url) {
                $articleTag = new ArticleImage();
                $articleTag->article_id = $article->id;
                $articleTag->url = $url;
                $articleTag->home_id = $article->home_id;
                $articleTag->saveModel($transaction);
            }

            CategoryTag::updateAllCounters(['article_count' => 1], ['id' => $tagList]);
            Category::updateAllCounters(['article_count' => 1], ['id' => $article->category_id]);
            HomeFurniture::updateAllCounters(['article_count' => 1], ['id' => $furnitureList]);
            HomeRoom::updateAllCounters(['article_count' => 1], ['id' => $article->room_id]);

//
//            $result = [
//                'id' => $category->id,
//                'name' => $category->name,
//                'articleCount' => 0,
//                'tagList' => CategoryTag::find()->select(CategoryTag::getListField())->asArray()->all(),
//            ];


            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return [
            'id' => $article->id,
        ];
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


}