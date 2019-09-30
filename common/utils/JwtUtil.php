<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/5/9
 * Time: 17:32
 */

namespace common\utils;

use Firebase\JWT\JWT;
use Yii;

class JwtUtil
{
    public static $key = 'DdgP8fcWGponbMCwcQ';

    /**
     * 过期之后多长时间内可用
     * @var int
     */
    public static $leeway = 120;

    public static $duration = 86400 * 7;
    /**
     * 根据key字符串和token获取数据
     * @param string $token
     * @return array
     */
    public static function getDataByKey($token)
    {
        Jwt::$leeway = self::$leeway;
        $decoded = JWT::decode($token, static::$key, ['HS256']);
        return json_decode(json_encode($decoded), true);
    }

    /**
     * 根据key字符串生成token
     * @param array $data
     * @param array $config
     * @return string
     */
    public static function getTokenByKey($data, $config = [])
    {
        $key = static::$key;
        $nowtime = time();
        $toEncode = [
            'iss' => 'http://api.admin.com', //签发者
            'aud' => 'http://api.admin.com', //jwt所面向的用户
            //'iat' => $nowtime, //签发时间 暂时不启用发送时间验证
            //'nbf' => $nowtime + 10, //在什么时间之后该jwt才可用
            'exp' => $nowtime + JwtUtil::$duration, //过期时间 30min
        ];
        if (!empty($config)) {
            $toEncode = array_merge($toEncode, $config);
        }
        $toEncode['data'] = $data;
        return JWT::encode($toEncode, $key, 'HS256');
    }

}