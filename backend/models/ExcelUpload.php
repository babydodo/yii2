<?php
namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Excel文件上传模型类
 *
 * @property UploadedFile $file
 */
class ExcelUpload extends Model
{
    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => '文件',
        ];
    }

}