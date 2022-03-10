<?php

/**
 * Класс для формы контакта
 */
namespace app\models;

use Yii;
use yii\base\Model;

class Contact extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;
    public $sendOk;

    public function attributeLabels()
    {
        return [
            'name' => 'Имя пользователя',
            'email' => 'Почта',
            'subject' => 'Предмет обращения',
            'body' => 'Текст обращения'
        ];
    }

    public function rules()
    {
        return [
            [['name', 'email', 'subject', 'body', 'sendOk'], 'safe'],
            ['email', 'email', 'message' => 'Неверный формат эл.почты'],
            [['name', 'email', 'subject', 'body'], 'required'],
            ['verifyCode', 'captcha'],
        ];
    }

    public function send()
    {
        $message = Yii::$app->mailer->compose();
        $message->setFrom(Yii::$app->params['adminEmail']);
        $this->sendOk = $message->setTo(Yii::$app->params['adminEmail'])
            ->setReplyTo([$this->email => $this->name])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
        return $this->sendOk;
    }
}
?>
