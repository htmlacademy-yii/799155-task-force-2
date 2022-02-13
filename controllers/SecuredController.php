<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;

abstract class SecuredController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                        'denyCallback' => function ($rule, $action) {
                            throw new HttpException(401, "Вы не авторизованы!");
                        }
                    ]
                ]
            ]
        ];
    }

    /**
     * Переопределение метода
     * Не авторизованный пользователь всегда будет перенаправлен на страницу лендинга
     */
    public function beforeAction($action)
    {
        if (Yii::$app->helpers->checkAuthorization() === null) {
            $this->redirect('/site');
            return false;
        }
        return true;
    }
}
