<?php

namespace app\controllers;

use Yii;
use app\models\UsersSelector;
use app\models\Task;
use app\models\Category;
use app\models\ReviewsSelector;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\Profile;
use app\models\ProfileData;
use app\models\ProfileFile;
use yii\web\UploadedFile;
use app\models\Categories;
use app\models\User;
use app\models\Password;
use yii\bootstrap4\ActiveForm;
use yii\web\Response;

class UsersController extends SecuredController
{
    public function actionView(int $id)
    {
        $user = UsersSelector::selectUser($id);
        $profile = Profile::findOne(['user_id' => $id]);
        if (!$user->contractor) {
            throw new NotFoundHttpException('У Вас нет доступа к этой странице');
        }
        $reviews = ReviewsSelector::getReviews($id, [Task::STATUS_DONE, Task::STATUS_REFUSED]);
        return $this->render('view', [
            'user' => $user,
            'reviews' => $reviews,
            'profile' => $profile,
        ]);
    }

    public function actionEditProfile(int $id)
    {
        if (Yii::$app->helpers->checkAuthorization()->id !== $id) {
            throw new ForbiddenHttpException('Вам запрещён доступ к этому профилю!');
        }
        $categoryNames = Category::getCategoryNames();
        $prof = Profile::findOne(['user_id' => $id]);
        $user = User::findOne($id);
        $profile = new ProfileData($prof, $user);
        $avatar = new ProfileFile();
        if (!empty($prof->categories)) {
            $profile->categoriesCheckArray = ProfileData::decodeCategories($prof->categories);
        }

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('modal') === 'file') {
                $avatar->file = UploadedFile::getInstance($avatar, 'file');
                if ($avatar->validate()) {
                    if ($avatar->updateProfile($prof, $user)) {
                        return $this->refresh();
                    }
                }
            }
            if (Yii::$app->request->post('form') === 'save') {
                $profile->load(Yii::$app->request->post());
                $prof->categories = ProfileData::codeCategories($profile->categoriesCheckArray);
                if ($profile->validate()) {
                    $profile->updateProfile($prof, $user);
                }
            }
        }
        return $this->render('edit-profile', [
            'model' => $profile,
            'avatar' => $avatar,
            'catNames' => $categoryNames,
        ]);
    }

    public function actionChangePassword(int $id)
    {
        if (Yii::$app->helpers->checkAuthorization()->id !== $id) {
            throw new ForbiddenHttpException('Вам запрещён доступ!');
        }
        $user = User::findOne($id);
        $pwd = new Password($user);
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('replace') === 'ok') {
                $pwd->load(Yii::$app->request->post());
                if ($pwd->validate()) {
                    $pwd->updatePassword($user);
                    return $this->redirect('/edit-profile/' . $user->id);
                }
            }
            if (Yii::$app->request->post('back') === 'cancel') {
                return $this->goBack();
            }
        }
        return $this->render('change-password', ['model' => $pwd,]);
    }
}
