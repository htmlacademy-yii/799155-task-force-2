<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Registration;
use app\models\Logon;
use app\models\City;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Profile;
use TaskForce\exception\TaskForceException;

class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                    'logon' => ['post', 'get'],
                    'registration' => ['post', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['registration', 'logon', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'matchCallback' => function ($rule, $action) {
                            if (is_object(Yii::$app->user)) {
                                if (is_numeric(Yii::$app->user->getId())) {
                                    return true;
                                }
                                throw new TaskForceException('Вы не авторизованы!');
                            }
                            throw new TaskForceException('Пожалуйста, выполните вход!');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['registration', 'logon'],
                        'roles' => ['?'],
                    ],
                ]
            ]
        ];
    }

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
        if (is_object(Yii::$app->user)) {
            $id = Yii::$app->user->getId();
            $profile = Profile::findOne(['user_id' => $id]);
            if ($profile) {
                $profile->last_act = date("Y-m-d H:i:s");
                $profile->update();
            }
            Yii::$app->user->logout();
        }
        return $this->goHome();
    }
}
