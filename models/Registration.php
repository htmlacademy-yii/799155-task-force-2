<?php

namespace app\models;

use app\models\User;
use app\models\City;
use Yii;
use TaskForce\exception\TaskForceException;

class Registration extends User
{
    public $password_repeat;
    public $city_name;

    public function rules()
    {
        return [
            [['name', 'email', 'password', 'contractor'], 'required'],
            [['name', 'email', 'password', 'password_repeaat', 'city_name', 'contractor'], 'safe'],
            [['name', 'email', 'password', 'password_repeat', 'city_name'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['contractor'], 'integer'],
            ['password', 'compare', 'message' => 'Оба пароля должны совпадать'],
        ];
    }
    
    /**
     * Регистрация подьзователя
     * @param Registration $model данные формы регистрации
     * @param array $cities массив имен городов, передаваемых в форму регистрации
     *
     * @return true|false в случае, если форма не содержала ошибок - true
     */
    public static function registerUser(Registration $model, array $cities): bool
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            $userData = [
                'name' => $model->name,
                'email' => $model->email,
                'password' => password_hash($model->password, PASSWORD_DEFAULT),
                'contractor' => $model->contractor,
                'city_id' => City::getId($cities[$model->city_name]),
            ];
            //сохраняем в бд
            $user = new User();
            $user->attributes = $userData;
            if (!$user->save()) {
                $error = Yii::$app->helpers->getFirstErrorString($user);
                throw new TaskForceException('User error ' . $error);
            }
            return true;
        }
        return false;
    }
}
