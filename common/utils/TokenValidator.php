<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/5/9
 * Time: 17:42
 */

namespace common\utils;


use common\exceptions\TokenException;
use Yii;
use yii\filters\auth\AuthMethod;

class TokenValidator extends AuthMethod
{

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        try{
            if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
                $token = $matches[1];
            } elseif (!empty($request->get('token'))) { // 兼容Get方式传递token
                $token = $request->get('token');
            } else {
                throw new TokenException(TokenException::NEED_LOGIN);
            }

            /*
             * 先验证过期时间，再校验token
             */
            $data = json_decode(base64_decode(explode('.', $token)[1]), true);
            if (is_array($data) && isset($data['exp'])) {
                if ((int) $data['exp'] + JwtUtil::$leeway < time()) {
                    throw new TokenException(TokenException::NEED_LOGIN, '登录已过期');
                }
            } else {
                throw new TokenException(TokenException::INVALID);
            }
            $data = JwtUtil::getDataByKey($token);
        } catch (\Exception $e) {
            if ($e->getCode() === TokenException::NEED_LOGIN) {
                throw $e;
            }
            throw new TokenException(TokenException::INVALID, '大侠，请手下留情');
        }
        $identity = $user->loginByAccessToken($data['data']['user_id'], get_class($this));
        if ($identity == null) {
            throw new TokenException(TokenException::UNKNOWN, '账号不存在');
        }

        $_GET['default_home_id'] = $data['data']['home_id'];
        return $identity;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response)
    {
        throw new TokenException(TokenException::NEED_LOGIN, '授权无效,请重新登录');
    }
}