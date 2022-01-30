<?php

namespace app\models;

class ReviewsSelector extends Review
{
    public $avatar; //аватар заказчика-автора отзыва
    public $name; //наименование задачи
    public $status; //статус задачи
    public $stars; //массив звезд рейтинга

    /**
     * Возвращает отзыв для исполнителя о его работе
     * @param int $userId id исполнителя
     * @param TaskSelector $task задание
     * @return Review $review отзыв
     */
    public static function selectReview(int $userId, TasksSelector $task): ?object
    {
        $query = self::find()->select([
            'reviews.id',
            'rating',
            'reviews.custom_id',
            'comment',
            'reviews.add_date as add_date',
            'profiles.avatar as avatar',
            'tasks.name as name',
            'tasks.status as status',
            'task_id'
        ])->where(['task_id' => $task->id]);
        $query = $query->innerJoin('tasks', 'tasks.id = task_id');
        $query = $query->innerJoin('profiles', 'reviews.custom_id = profiles.user_id');
        $review = $query->one();
        if ($review) {
            $review->stars = [];
            $review->stars = array_pad($review->stars, round($review->rating), true);
            $review->stars = array_pad($review->stars, 5, false);
            switch ($review->status) {
                case TasksSelector::STATUS_DONE:
                    $review->status = 'выполнено';
                    break;
                case TasksSelector::STATUS_REFUSED:
                    $review->status = 'провалено';
                    break;
            }
            return $review;
        }
        return null;
    }

    /**
     * Возвращает массив отзывов об исполнителе
     * @param int $userId id исполнителя
     * @param array|null $taskStatuses требуемый статус задачи
     * @return array возвращает массив отзывов
     */
    public static function getReviews(int $userId, array $taskStatuses = null, int $limit = null)
    {
        $tasks = TasksSelector::selectTasksByContractor($userId, $taskStatuses);
        $reviews = [];
        foreach ($tasks as $task) {
            $review = self::selectReview($userId, $task);
            if ($review) {
                $reviews[] = $review;
                if ($limit and (count($reviews) === $limit)) {
                    break;
                }
            }
        }
        return $reviews;
    }

    /**
     * Возвращает рейтинг исполнителя
     * @param int $userId id исполнителя
     * @return array возвращает массив [int количество отзывов, float рейтинг]
     */
    public static function getRating(int $userId): array
    {
        //отзывы о выполненных заданиях
        $reviews = self::getReviews($userId, [TasksSelector::STATUS_DONE]);
        if (count($reviews) === 0) {
            return array(0, 0);
        }
        //проваленные задания
        $refusedTasks = TasksSelector::selectTasksByContractor($userId, [TasksSelector::STATUS_REFUSED]);
        $refusedCount = count($refusedTasks);
        $score = array_reduce($reviews, function ($result, $item) {
            $result += $item->rating;
            return $result;
        }, 0);

        $rating = $score / (count($reviews) + $refusedCount);
        return array(count($reviews), $rating);
    }
}
