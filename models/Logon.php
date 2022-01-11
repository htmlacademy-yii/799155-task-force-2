<?php

namespace app\models;

use yii\base\Model;

class Logon extends Model
{
    public $password;
    public $email;
    public $user;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
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
            return true;
        }
        return false;
    }
}
