<?php

namespace app\models;

use Yii;
use TaskForce\exception\TaskForceException;

class Registration extends User
{
    public $password_repeat;
    public $city_name;

    public function rules()
    {
        return [
            [['name', 'email', 'password', 'password_repeaat', 'city_name', 'contractor'], 'safe'],
            [['name', 'email', 'password', 'password_repeat', 'city_name'], 'string', 'max' => 64],
            [['email'], 'unique', 'message' => 'почтовый адрес должен быть уникальным'],
            [['contractor'], 'integer'],
            [['password'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            [['password_repeat'], 'required', 'message' => 'Поле пароля не может быть пустым'],
            ['password', 'compare', 'message' => 'Оба пароля должны совпадать'],
            [['name'], 'required', 'message' => 'Поле имени не может быть пустым'],
            [['email'], 'required', 'message' => 'Поле эл.почты не может быть пустым'],
            [['city_name'], 'required', 'message' => 'Поле города не может быть пустым'],
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
            if ($model->contractor === null) {
                    $model->contractor = '0';
            }
            $userData = [
                'name' => $model->name,
                'email' => $model->email,
                'password' => password_hash($model->password, PASSWORD_DEFAULT),
                'contractor' => $model->contractor,
                'city_id' => City::getId($cities[$model->city_name]),
            ];
            //сохраняем данные пользователя в бд
            $user = new User();
            $user->attributes = $userData;
            if (!$user->save()) {
                $error = Yii::$app->helpers->getFirstErrorString($user);
                throw new TaskForceException('Ошибка регистрации: ' . $error);
            }
            //создадим профиль пользователя (пока пустой)
            $user = User::findOne(['email' => $userData['email']]);
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->last_act = date("Y-m-d H:i:s");
            if (!$profile->save()) {
                $error = Yii::$app->helpers->getFirstErrorString($profile);
                throw new TaskForceException('Ошибка создания профиля: ' . $error);
            }
            return true;
        }
        return false;
    }
}
