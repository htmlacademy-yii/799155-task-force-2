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
use app\models\Source;
use app\models\Location;
use yii\web\ForbiddenHttpException;
use TaskForce\logic\Client;

class AuthController extends Controller
{
    public function actions()
    {
        return [
            'vkontakte' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'actionVkontakte'],
            ],
        ];
    }

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
                            if (Yii::$app->helpers->checkAuthorization() === null) {
                                throw new ForbiddenHttpException('Вы не авторизованы!');
                            }
                            return true;
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
        if (Registration::registerUser($model)) {
            $this->goHome();
        }
        return $this->render('registration', [
            'model' => $model,
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

    /**
     * Регистрация через аккаунт ВКонтакте
     * @param Object $client - объект, передаваемый API VKontakte
     */
    public function actionVkontakte($client)
    {
        if ($client) {
            $model = new Client($client);
            $model->authorizeClient();
        }
    }
}
