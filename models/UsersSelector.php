<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\TasksSelector;
use app\models\ReviewsSelector;
use TaskForce\exception\TaskForceException;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class UsersSelector extends User
{
    public $doneCounter;   //количество выполненных заданий
    public $refuseCounter; //количество проваленных заданий
    public $about_info;
    public $born_date;
    public $address;
    public $avatar;
    public $city;
    public $stars; //массив звезд рейтинга
    public $rating;
    public $status;
    public $position; //положение в рейтинге
    public $phone;
    public $messenger;
    public $categories;
    public $customers = []; //массив заказчиков для этого исполнителя

    /**
     * Возвращает пользователя с заданным id
     * @param int $userId id пользователя
     * @return object возвращает искомую сущность
     * Если пользователь не найден, вызывает исключение NotFoundHttpException
     */
    public static function selectUser(int $userId): object
    {
        $query = self::find()->select([
            'users.id',
            'users.name',
            'users.email',
            'users.add_date',
            'cities.name as city',
            'profiles.born_date as born_date',
            'profiles.address as address',
            'profiles.about_info as about_info',
            'profiles.avatar as avatar',
            'profiles.phone as phone',
            'profiles.messenger as messenger',
            'contractor',
        ]);
        $query->where(['users.id' => $userId]);
        $query = $query->
            leftJoin('cities', 'city_id = cities.id')->
            innerJoin('profiles', 'users.id = profiles.user_id');
        $user = $query->one();
        if ($user === null) {
            throw new TaskForceException('Пользователь id = ' . $userId . ' не найден!');
        }
        $pages = new Pagination();
        $pages->pageSize = ReviewsSelector::ALL_TASKS;
        $tasks = TasksSelector::selectTasksByStatus($userId, [TasksSelector::STATUS_DONE], $pages);
        $user->doneCounter = count($tasks);
        $tasks = TasksSelector::selectTasksByStatus($userId, [TasksSelector::STATUS_REFUSED], $pages);
        $user->refuseCounter = count($tasks);
        $tasks = TasksSelector::selectTasksByStatus($userId, [TasksSelector::STATUS_ON_DEAL], $pages);
        $user->status = count($tasks) ? self::STATUS_BUSY : self::STATUS_FREE;
        if (count($tasks)) {
            $user->customers = ArrayHelper::getColumn($tasks, 'custom_id');
        }
        $result = ReviewsSelector::getRating($userId);
        $user->stars = [];
        $user->stars = array_pad($user->stars, round($result[1]), true);
        $user->stars = array_pad($user->stars, 5, false);
        $user->rating = $result[1];
        $users = self::find()->select(['users.id'])->all();
        $positions = array_map(function ($item) {
            return ReviewsSelector::getRating($item->id)[1];
        }, $users);
        arsort($positions);
        $user->position = array_search($user->rating, array_values($positions)) + 1;
        if ($user->phone) {
            $user->phone = Yii::$app->helpers->translatePhoneNumber('+# (###) ###-##-##', $user->phone);
        }
        $profile = Profile::findOne(['user_id' => $userId]);
        $categoryIds = ProfileData::decodeCategories($profile->categories);
        $user->categories = Category::find()->select(['id', 'name'])->where(['in', 'id', $categoryIds])->all();
        return $user;
    }
}
