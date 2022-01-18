<?php

namespace app\models;

use Yii;

class RepliesSelector extends Reply
{
    public $contractor;
    public $avatar;
    public $rating;
    public $reviews; //int количество отзывов
    public $cat_id; //int id категории задания

    /**
     * Возвращает массив откликов на задание
     * @param int $taskId id задания
     * @return array возвращает массив откликов
     */
    public static function selectRepliesByTask(int $taskId): array
    {
        $query = self::find()->select([
            'replies.id',
            'replies.comment',
            'replies.contr_id',
            'replies.price',
            'replies.add_date',
            'replies.rating',
            'users.name as contractor',
            'profiles.avatar as avatar'
        ]);
        $query->where(['task_id' => $taskId, 'status' => null]);
        $query = $query->
            innerJoin('users', 'contr_id = users.id')->
            innerJoin('profiles', 'contr_id = profiles.user_id');
        $query = $query->limit(2)->offset(0)->orderBy(['add_date' => SORT_DESC]);
        $replies = $query->all();
        foreach ($replies as $reply) {
            $rating = ReviewsSelector::getRating($reply->contr_id);
            $reply->rating = [];
            $reply->rating = array_pad($reply->rating, round($rating[1]), true);
            $reply->rating = array_pad($reply->rating, 5, false);
            $reply->reviews = $rating[0];
        }
        return $replies;
    }
}
