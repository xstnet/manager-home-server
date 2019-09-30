<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: shantong
 * Date: 2018/9/10
 * Time: 18:26
 */

namespace frontend\controllers;


use common\exceptions\BaseException;
use common\utils\TokenValidator;
use yii\filters\Cors;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class BaseController extends Controller
{
	public static $userInfo;

	const CODE_SUCCESS = 0;
	const CODE_FAILED = 1;

	const MESSAGE_SUCCESS = 'ok';
	const MESSAGE_FAILED = '系统错误';

	public $layout = false;

	public $enableCsrfValidation = false;

	public function behaviors()
	{
        return array_merge(
            parent::behaviors(),
            [
                /**
                 * 验证Token
                 */
                'tokenValidator' => [
                    'class' => TokenValidator::className(),
                    'optional' => [
                        'login',
                        'register',
                        'test',
                    ],
                    'except' => [
                        'error',
                    ],
                ],
                [
                    'class' => Cors::className(),
                ]
            ]
        );
	}

	public static function returnJson($code = self::CODE_SUCCESS, $message = self::MESSAGE_SUCCESS, $data = [])
    {
        // 当code 值为数组时， 当做本次成功，直接当做返回数据
        if (is_array($code)) {
            $data = $code;
            $code = self::CODE_SUCCESS;
            $message = self::MESSAGE_SUCCESS;
        }
        // 当code传值为字符串时， message取code， data取message
        if (is_string($code)) {
            $data = $message;
            $message = $code;
            $code = self::CODE_SUCCESS;
        }
        // data的值不能为字符串
        if (!is_array($data)) {
            $data = [];
        }
        // 处理code不为0的情况
        if ($code > 0) {
            if ($message === '') {
                $message = self::MESSAGE_FAILED;
            }
        }
        if ($code === null) {
            $code = self::CODE_SUCCESS;
        }
        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        return $result;
    }

	public static function formatHtml()
	{
		Yii::$app->response->format = Response::FORMAT_HTML;
		Yii::$app->response->statusCode = 200;
	}

	/**
	 * @Desc:
	 * @param string $id
	 * @param array $params
	 * @return mixed
	 * @throws BaseException
	 */
	public function runAction($id, $params = [])
	{
		try {
			return parent::runAction($id, $params); // 捕获所有action方法异常，格式化返回
		} catch (\Exception $e) {
			Yii::error($e->getMessage());
			Yii::error($e->getTraceAsString());
			$message = $e->getMessage();
			throw new BaseException(10001, $message);
		}
	}

	/**
	 * @Desc: 获取Get参数
	 * @param string $key
	 * @param string $defaultValue
	 * @return array|mixed
	 */
	public static function getParams($key = '', $defaultValue = '')
	{
		if (empty($key)) {
			return Yii::$app->request->get();
		}

		return Yii::$app->request->get($key, $defaultValue);
	}

	/**
	 * @Desc: 获取post参数
	 * @param string $key
	 * @param string $defaultValue
	 * @return array|mixed
	 */
	public static function postParams($key = '', $defaultValue = '')
	{
		if (empty($key)) {
			return Yii::$app->request->post();
		}

		return Yii::$app->request->post($key, $defaultValue);
	}
}