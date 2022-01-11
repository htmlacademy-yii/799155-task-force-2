<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Registration;
use app\models\Logon;
use app\models\City;
use app\models\User;

class AuthController extends Controller
{
    public function actionRegistration()
    {
        $model = new Registration();
        $cities = array_values(City::getCityNames());
        if (Registration::registerUser($model, $cities)) {
            $this->goHome();
        }
        return $this->render('registration', [
            'model' => $model,
            'cities' => $cities,
        ]);
    }

    public function actionLogon()
    {
        $model = new Logon();
        $request = Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            $user = User::findOne(['email' => $model->email]);
            if ($model->logon($user)) {
                return $this->goBack();
            }
        }
        $model->password = '';
        return $this->render('logon', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        //Yii::$app->user->logout();
        return $this->goHome();
    }
}
