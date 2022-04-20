<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\PasswordValidator;
use TaskForce\logic\Client;

/**
 * Класс служит для проверки и смены пароля
 */

class Password extends Model
{
    //текущий хэш-пароль
    public $userPasswordHash;
    //текущий пароль, который введет пользователь
    public $userPasswordOld;
    //новый пароль
    public $password;
    //повтор нового пароля
    public $password_repeat;
    public $userId;
    public $email;

    public function __construct($user)
    {
        $this->userPasswordHash = $user->password;
        if (strlen($this->userPasswordHash) < Client::PASSWORD_LENGTH + 1) {
            //была авторизация через ВКонтакте и текущий пароль - фиктивный
            $this->userPasswordOld = 'Введите любое значение';
        }
        $this->userId = $user->id;
    }

    public function rules()
    {
        return [
            [['userPasswordOld', 'password', 'password_repeat', 'userPasswordHash'], 'safe'],
            [['userPasswordOld', 'password', 'password_repeat'], 'string'],
            [['userPasswordOld'], 'required', 'message' => 'Поле не может быть пустым'],
            ['userPasswordOld', PasswordValidator::class],
            [['password'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            [['password_repeat'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            ['password', 'compare', 'message' => 'Оба пароля должны совпадать'],
        ];
    }

    /**
     * Запись нового пароля в базу
     * @param Object $user - авторизованный пользователь
     * @return bool true, если пароль заменен успешно
     */
    public function updatePassword($user): bool
    {
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        return $user->update(false, ['password']) !== false;
    }
}
