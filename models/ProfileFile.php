<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
//use \yii\db\ActiveRecord;
use yii\base\Model;

class ProfileFile extends Model
{
    public const AVATAR_ANONIM = '/img/logo.png';
    public $file;

    public function attributeLabels()
    {
        return [
            'file' => 'Аватар',
        ];
    }

    public function rules()
    {
        return [
            ['file', 'safe'],
            ['file', 'required', 'message' => 'Выберите изображение'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 256 * 256],
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        if (empty($this->file)) {
            return false;
        }
        $mimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];
        return Yii::$app->helpers->validateUploadedFile($this->file, $mimeTypes);
    }

    public function updateProfile($prof, $user): bool
    {
        if ($this->file !== null) {
            $newName = uniqid('up') . '.' . $this->file->extension;
            $prof->avatar = Yii::$app->params['uploadPath'] . $newName;
            move_uploaded_file($this->file->tempName, Yii::$app->basePath . '/web' . $prof->avatar);
            return $prof->update();
        }
        return false;
    }
}
