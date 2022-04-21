<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use yii\base\Model;

/**
 * Класс для загрузки изображения аватара с локального
 * компьбтера в базу
 */
class ProfileFile extends Model
{
    public const AVATAR_ANONIM = '/img/logo.jpg';
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

    /**
     * Загрузка в профиль пользователя информации
     * об его аватаре
     * @param Profile $prof профиль пользователя
     * @param User $user зарегистрированный пользователь
     * @return bool true, если запись прошла успешно
     */
    public function updateProfile($prof, $user): bool
    {
        if ($this->file !== null) {
            $newName = uniqid('up') . '.' . $this->file->extension;
            $prof->avatar = Yii::$app->params['uploadPath'] . $newName;
            move_uploaded_file($this->file->tempName, Yii::$app->basePath . '/web' . $prof->avatar);
            return $prof->update() !== false;
        }
        return false;
    }
}
