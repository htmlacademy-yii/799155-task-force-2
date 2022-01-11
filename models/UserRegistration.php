<?php

namespace app\models;

use Yii;
use app\models\User;

class UserRegistration extends User
{
    public password_repeat;

    public static regiserUser(UserRegistration $registration)
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $registration->load($request->post());
            $user = User::find()->select([
                id,
                email,
            ])->where(['email' => $registration->email])-one();
            if (!$user) {
                //сохраняем в бд
            }
        }
    }
}
