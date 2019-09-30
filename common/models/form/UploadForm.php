<?php
/**
 *
 * Created by PhpStorm.
 * Author: Xu shantong <shantongxu@qq.com>
 * Date: 2019/9/27
 * Time: 14:20
 */

namespace common\models\form;

use common\exceptions\ParameterException;
use common\models\BaseModel;
use common\models\UploadFile;
use Yii;
use yii\web\UploadedFile;

/**
 * Upload form
 */
class UploadForm extends \yii\base\Model
{
	const SCENARIO_IMAGE_FILE = 'image_file'; // 上传图片文件场景
	const SCENARIO_IMAGE_DATA = 'image_data'; // 上传图片内容场景

    /**
     * 超过这个值才进行压缩
     * 单位 字节(B)
     */
    const SIZE = 1024 * 1024;
	
	/**
	 * @var UploadedFile
	 */
	public $imageFile; // 图片文件

    /**
     * @var UploadedFile[]
     */
    public $imageFiles;
	
	public $imageData; // 图片内容

	public function scenarios()
	{
		return array_merge(
			parent::scenarios(),
			[
				self::SCENARIO_IMAGE_FILE => ['imageFile',],
				self::SCENARIO_IMAGE_DATA => ['imageData',],
			]
		);
	}

    public function formName()
    {
        return '';
    }

    public function rules()
	{
		
		return [
			[
				['imageFile'],
				'file',
				// mime不需要和后缀一致
				'checkExtensionByMimeType' => false,
				// 不能为空
				'skipOnEmpty' => true,
                'maxFiles' => 10,
				'uploadRequired' => '请上传正确的文件',
				// 文件格式
				'extensions' => ['jpg', 'png', 'jpeg'],
				'wrongExtension' => '请上传JPG、PNG文件',
				// 大小上限
				'maxSize' => 12 * 1024 * 1024,
				'tooBig' => '文件大小上限12M',
				'on' => self::SCENARIO_IMAGE_FILE,
			],
			[
				'imageData',
				'required',
				'message' => '请上传正确的文件',
				'on' => self::SCENARIO_IMAGE_DATA,
			],
		];
	}
	
	/**
	 * 通过文件上传
	 */
    public function uploadImageFile()
    {
        if ($this->validate()) {
            try {
                $ret = $this->saveFile();
                if ($ret) {
                    return $ret;
                }

            } catch (\Exception $e) {
                $this->addError('imageFile', $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function uploadImageFiles()
    {
        $result = [];
        try {
            foreach ($this->imageFiles as $file) {
                $this->imageFile = $file;
                $uploadRet = $this->saveFile();
                if ($uploadRet === false) {
                    return false;
                }
                $result[] = $uploadRet;
            }

        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }

    public function saveFile()
    {
        $saveDomain = 'http://static.manager-home.xstnet.com';
        $this->compressImage();
        $imageFile = $this->imageFile;
        $fileMd5 = md5_file($imageFile->tempName);
        // 查询文件是否存在
        $result = UploadFile::findOne(['md5' => $fileMd5]);
        if (!empty($result)) {
            return $result->path;
        }

        // 上传文件
        $baseDir = '/uploads/images/' . date('Y-m') . '/';

        $saveDir = $saveDomain . \Yii::getAlias('@uploads') . '/images/' . date('Y-m') . '/';

        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }

        $filepath = $baseDir . $fileMd5 . '.' . $imageFile->extension;

        $savePath = $saveDir . $fileMd5 . '.' . $imageFile->extension;


        // 添加文件记录
        if ($imageFile->saveAs($savePath)) {
            $fileModel = new UploadFile();
            $fileModel->name = $fileMd5;
            $fileModel->md5 = $fileMd5;
            $fileModel->path = $filepath;
            $fileModel->size = $imageFile->size;
            $mimeType = \yii\helpers\FileHelper::getMimeType($savePath);
            $fileModel->mime_type = $mimeType ? : $imageFile->type;
            $fileModel->extend = $imageFile->extension;
            $fileModel->saveModel();

            return $filepath;
        }
        throw new ParameterException(ParameterException::INVALID, '上传失败');
    }


    /**
	 * 通过文件内容上传
	 */
	public function uploadImageData()
	{
		if ($this->validate()) {
			$this->compressImage();
			$imageData = str_replace(' ', '+', $this->imageData);
			$fileContent = base64_decode($imageData);
			$fileHash = sha1($fileContent);
			$fileExt = 'jpg';
			$filePath = $this->imageBasePath . '/' . substr($fileHash, 0, 2) . '/';
			$fileName = $filePath . $fileHash . '.' . $fileExt;
//			if ($ret['code'] === 0) {
//				return [
//					'name' => $fileHash . '.' . $fileExt,
//					'sha1' => $fileHash,
//					'url' => $ret['result']['url'],
//					'extension' => $fileExt,
//					'size' => strlen($fileContent),
//				];
//			}
//			Yii::error(sprintf('code: %s, msg: %s', $ret['code'], $ret['message']), 'upload-image-data');
			$this->addError('imageData', '上传失败');
		}
		return false;
	}
	
	public function compressImage()
	{
		$image = $this->imageFile;
		// 150Kb 之内的图片不压缩
		if ($this->imageFile->size <= self::SIZE) {
			return true;
		}
		list($width, $height, $type) = getimagesize($image->tempName);
		$imageinfo = array(
			'width' => $width,
			'height' => $height,
			'type' => image_type_to_extension($type,false),
		);
		$fun = "imagecreatefrom" . $imageinfo['type'];
		$newImage = $fun($image->tempName);
		
		// 保持比例压缩
		$maxSize = max($imageinfo['width'], $imageinfo['height']);
		$size = 1000;
		if ($maxSize > $size) {
			$ratio = $size / $maxSize;
		} else {
			$ratio = 1;
		}
		$new_width = $imageinfo['width'] * $ratio;
		$new_height = $imageinfo['height'] * $ratio;
		$image_thump = imagecreatetruecolor($new_width,$new_height);
		
		//将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
		imagecopyresampled($image_thump,$newImage,0,0,0,0,$new_width,$new_height,$imageinfo['width'],$imageinfo['height']);
		imagedestroy($newImage);
		$exif = exif_read_data($image->tempName);
		// 纠正图片方向
		if(!empty($exif['Orientation'])) {
			Yii::error($exif);
			switch($exif['Orientation']) {
				case 8:
					$image_thump = imagerotate($image_thump,90,0);
					break;
				case 3:
					$image_thump = imagerotate($image_thump,180,0);
					break;
				case 6:
					$image_thump = imagerotate($image_thump,-90,0);
					break;
			}
		}
		$funcs = "image".$imageinfo['type'];
		// 压缩等级，0-100  越低压缩比越高, 默认75
		$funcs($image_thump, $image->tempName, 80);
		
		unset($newImage, $image_thump, $funcs, $exif, $new_height, $new_width);
		
		// 更新压缩后的文件大小
		$this->imageFile->size = filesize($image->tempName);
	}
	
}