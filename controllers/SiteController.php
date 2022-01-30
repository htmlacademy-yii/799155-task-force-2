<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\TasksSelector;
use app\models\Categories;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionSite()
    {
        $tasks = TasksSelector::selectTasks(new Categories(), [TasksSelector::STATUS_NEW], 4, 0);
        return $this->render('landing', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (is_object(Yii::$app->user)) {
            $user = User::findIdentity(Yii::$app->user->getId());
            if (is_object($user)) {
                return $this->redirect(['/tasks']);
            }
        }
        return $this->actionSite();
    }
}
