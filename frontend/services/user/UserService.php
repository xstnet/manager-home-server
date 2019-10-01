<?php
/**
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/5/12
 * Time: 20:48
 */

namespace frontend\services\user;


use common\helpers\Helpers;
use common\models\Home;
use common\models\HomeMember;
use common\models\User;
use common\models\UserHome;
use common\utils\JwtUtil;
use frontend\services\BaseService;
use common\exceptions\ParameterException;
use Yii;

class UserService extends BaseService
{
    public function register($params)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // 添加用户
            $userParams = [
                'username' => trim($params['username'] ?? ''),
                'mobile' => trim($params['mobile'] ?? ''),
                'nickname' => trim($params['nickname'] ?? ''),
            ];
            $userParams['nickname'] = empty($userParams['nickname']) ? $userParams['username'] : $userParams['nickname'];
            $password = trim($params['password'] ?? '');
            if (strlen($password) < 6 || strlen($password) > 20) {
                throw new ParameterException(ParameterException::INVALID, '密码长度在6-20位之间');
            }
            $user = new User();
            $user->setAttributes($userParams);
            $user->setPassword($password);
            $user->saveModel($transaction);

            // 为该用户创建家
            $home = new Home();
            $home->master_user_id = $user->id;
            $home->creator = $user->id;
            $home->name = $user->nickname . '的家';
            $home->saveModel($transaction);

            // 用户关联家
            $userHome = new UserHome();
            $userHome->home_id = $home->id;
            $userHome->user_id = $user->id;
            $userHome->is_default = UserHome::IS_DEFAULT_YES;
            $userHome->saveModel($transaction);

            // 把自己加入家庭成员表
            $homeMember = new HomeMember();
            $homeMember->home_id = $home->id;
            $homeMember->user_id = $user->id;
            $homeMember->saveModel($transaction);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $tokenData = [
            'user_id' => $user->id,
            'home_id' => $home->id,
            'username' => $user->username
        ];
        $token = JwtUtil::getTokenByKey($tokenData);
        return [
            'token' => $token,
            'home_id' => $home->id,
        ];
    }

    public function getUserInfo()
    {
        $homeId = Helpers::getHomeId();

        $userId = Yii::$app->user->id;

        $homeList = UserHome::find()
            ->select([
                'home.id', 'name' => 'home.name',
                'masterId' => 'home.master_user_id',
                'isDefault' => 'is_default',
            ])
            ->alias('uh')
            ->leftJoin(['home' => Home::tableName()], 'home.id = uh.home_id')
            ->where(['user_id' => $userId])
            ->indexBy('id')
            ->asArray()
            ->all();

        $familyMember = HomeMember::find()
            ->select('user.id, user.nickname')
            ->where(['home_id' => $homeId])
            ->alias('hm')
            ->leftJoin(['user' => User::tableName()], 'user.id = hm.user_id')
            ->asArray()
            ->all();


        $userInfo = [
            'id' => $userId,
            'username' => Yii::$app->user->identity->username,
            'nickname' => Yii::$app->user->identity->nickname,
            'mobile' => Yii::$app->user->identity->mobile,
            'homeName' => $homeList[Helpers::getHomeId()]['name'] ?? '',
            'homeList' => array_values($homeList),
            'familyMember' => $familyMember,
            'colorList' => [
                '',
                '#1cdd49',
                '#dd1625',
                '#dd8327',
                '#ddcfc3',
                '#000',
                '#1f10dd',
            ],
        ];

        return $userInfo;
    }


}