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
        $profile->categoriesCheckArray = ProfileData::decodeCategories($prof->categories);
        $result = 0;
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('modal') === 'file') {
                $avatar->file = UploadedFile::getInstance($avatar, 'file');
                if ($avatar->validate() and $avatar->updateProfile($prof, $user)) {
                    return $this->refresh();
                }
            }
            if (Yii::$app->request->post('form') === 'save') {
                $profile->load(Yii::$app->request->post());
                if ($profile->validate()) {
                    $prof->categories = ProfileData::codeCategories($profile->categoriesCheckArray);
                    $result = $profile->updateProfile($prof, $user) ? 1 : 0;
                }
            }
        }
        return $this->render('edit-profile', [
            'model' => $profile,
            'avatar' => $avatar,
            'catNames' => $categoryNames,
            'result' => $result,
        ]);
    }

    public function actionChangePassword(int $id)
    {
        if (Yii::$app->helpers->checkAuthorization()->id !== $id) {
            throw new ForbiddenHttpException('Вам запрещён доступ!');
        }
        $user = User::findOne($id);
        $pwd = new Password($user);
        $result = false;
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('replace') === 'ok') {
                $pwd->load(Yii::$app->request->post());
                if ($pwd->validate()) {
                    $result = $pwd->updatePassword($user);
                }
            }
            if (Yii::$app->request->post('back') === 'cancel') {
                return $this->goBack();
            }
        }
        return $this->render(
            'change-password',
            [
                'model' => $pwd,
                'result' => $result,
                'url' => '/edit-profile/' . $user->id,
            ]
        );
    }
}
