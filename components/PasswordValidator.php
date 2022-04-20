<?php

namespace app\components;

use Yii;
use yii\validators\Validator;
use yii\caching\TagDependency;
use app\models\User;
use TaskForce\logic\Client;

/**
 * Класс служит для валидации поля ввода пароля
 */

class PasswordValidator extends Validator
{
    const TEST_WEAK = 0;
    const TEST_STRONG = 1;

    public $strength;

    private $weak_pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
    private $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';

    public function init()
    {
        parent::init();
        $this->message = 'Введен неверный пароль';
    }

    /**
     * Проверка пароля на стороне сервера
     * Выводит в форму сообщение об ошибке
     * @param Password $model объект класса
     * @param string $attribute имя проверяемого атрибута
     */
    public function validateAttribute($model, $attribute)
    {
        $oldPasswordHash = User::findOne($model->userId)->password;
        if (strlen($oldPasswordHash) < Client::PASSWORD_LENGTH + 1) {
            //была авторизация через ВКонтакте и текущий пароль - фиктивный
            return;
        }
        if (!password_verify($model->userPasswordOld, $model->userPasswordHash)) {
            $this->addError($model, $attribute, 'Указан неверный пароль');
        }
    }

    /**
     * Проверка пароля на стороне клиента
     * @param Password $model объект класса
     * 
     * @return string js-сообщение об ошибке или ничего
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $password = $model->userPasswordOld;
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $pass = json_encode($password, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $res = password_verify($password, $model->userPasswordHash) ? 1 : 0;
        return <<< JS
            if ($res == 0) {
                messages.push($message);
                console.log('fail verify password ' + $pass);
            } else {
                console.log('success verify password ' + $pass);
            }
        JS;
    }

    /**
     * Проверка силы пароля на стороне сервера
     * @param CModel $model модель валидации
     * @param string $attribute атрибут, который валидируется в данный момент
     */
    protected function validatePower($attribute, $params){
        // проверяем параметр strength и выбираем паттерн для preg_match
        if ($this->strength === self::TEST_WEAK) {
            $pattern = $this->weak_pattern;
        } elseif ($this->strength === self::TEST_STRONG) {
            $pattern = $this->strong_pattern;
        }
        // получаем значение атрибута модели и проверяем по паттерну
        if (!preg_match($pattern, $this->password_repeat)) {
            $this->addError($attribute, 'Пароль слишком слабый');
        }
    }
}
