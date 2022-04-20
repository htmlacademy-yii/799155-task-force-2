<?php

namespace app\models;

use Yii;

class Logon extends User
{
    protected $user;
    protected $source = false;

    public function rules()
    {
        return [
            [['password'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            [['email'], 'required', 'message' => 'Поле эл.почты не может быть пустым'],
            [['email'], 'email', 'message' => 'Неверный формат эл.почты'],
            [['email', 'password'], 'safe'],
            [['email', 'password'], 'string', 'max' => 64],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->user or !password_verify($this->password, $this->user->password)) {
                $this->addError($attribute, 'Указан неверный email или пароль');
            }
        }
    }

    /**
     * Производит авторизацию пользователя
     * @param User $user данные пользователя
     * @param bool $source источник данных для авторизации 
     * true - авторизация через ВКонтакте, false - через форму на сайте
     * 
     * @return bool результат авторизации
     */
    public function logon($user, $source = false): bool
    {
        $this->user = $user;
        $this->source = $source;
        if ($this->source or $this->validate()) {
            Yii::$app->user->login($user);
            return true;
        }
        return false;
    }
}
