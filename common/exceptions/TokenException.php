<?php
/**
 * Created by PhpStorm.
 * Desc: 参数异常
 * User: xstnet
 * Date: 2017/10/9
 * Time: 14:44
 */

namespace common\exceptions;


use yii\web\UnauthorizedHttpException;

class TokenException extends UnauthorizedHttpException
{
    const UNKNOWN = 30000;
    const INVALID = 30002;
    const NEED_LOGIN = 99;
    protected static $defaultMessage = '授权无效';

    public static $messages = [
        self::UNKNOWN => '参数错误',
        self::INVALID => '授权无效',
        self::NEED_LOGIN => '请先登录',
    ];

    public function getName()
    {
        return 'TokenException';
    }

    public function __construct($code = 1, $message = null)
    {
        if (empty($message)) {
            $message = isset(static::$messages[ $code ]) ?
                static::$messages[ $code ] :
                static::$defaultMessage;
        }
        parent::__construct($message, $code);
    }
}