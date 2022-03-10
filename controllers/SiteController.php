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
use app\models\Contact;
use yii\data\Pagination;

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
        $pages = new Pagination();
        $pages->pageSize = 4;
        $tasks = TasksSelector::selectTasks(new Categories(), [TasksSelector::STATUS_NEW], $pages);
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

    /**
     * Показывает форму для отправки сообщения администрации
     */
    public function actionContact()
    {
        $contact = new Contact();
        $contact->sendOk = false;
        if (!Yii::$app->user->isGuest) {
            $contact->name = Yii::$app->user->identity->name;
            $contact->email = Yii::$app->user->identity->email;
        }
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('contact-button') === 'ok') {
                $contact->load(Yii::$app->request->post());
                $contact->send();
            }
            if (Yii::$app->request->post('modal-button') === 'ok') {
                $contact->load(Yii::$app->request->post());
                if ($contact->sendOk) {
                    return $this->goBack();
                }
            }
        }
        return $this->render('contact', ['model' => $contact]);
    }
}
