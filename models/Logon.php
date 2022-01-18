<?php

namespace app\models;

use Yii;

class Logon extends User
{
    protected $user;

    public function rules()
    {
        return [
            [['password'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            [['email'], 'required', 'message' => 'Поле эл.почты не может быть пустым'],
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

    public function logon($user): bool
    {
        $this->user = $user;
        if ($this->validate()) {
            Yii::$app->user->login($user);
            return true;
        }
        return false;
    }
}
