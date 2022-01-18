<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\UsersSelector;
use app\models\TasksSelector;
use app\models\RepliesSelector;
use app\models\Category;
use app\models\ReviewsSelector;
use yii\web\NotFoundHttpException;

class UsersController extends SecuredController
{
    public function actionView(int $id)
    {
        $user = UsersSelector::selectUser($id);
        if (!$user->contractor) {
            throw new NotFoundHttpException('У Вас нет доступа к этой странице');
        }
        $reviews = ReviewsSelector::getReviews($id, [TasksSelector::STATUS_DONE, TasksSelector::STATUS_REFUSED]);
        return $this->render('view', [
            'user' => $user,
            'reviews' => $reviews,
        ]);
    }
}
