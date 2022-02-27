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
        $task = Task::findOne($taskId);
        $query = self::find()->select([
            'replies.id',
            'replies.comment',
            'replies.contr_id',
            'replies.price',
            'replies.add_date',
            'replies.status',
            'users.name as contractor',
            'profiles.avatar as avatar'
        ]);
        $replyStatuses = [
            Task::STATUS_NEW => Reply::STATUS_PROPOSAL,
            Task::STATUS_ON_DEAL => Reply::STATUS_ACCEPTED,
            Task::STATUS_DONE => Reply::STATUS_ACCEPTED,
            Task::STATUS_REFUSED => Reply::STATUS_REFUSED,
            Task::STATUS_CANCELED => '',
        ];
        $query->where(['task_id' => $taskId, 'status' => $replyStatuses[$task->status]]);
        if ($query->count() === 0) {
            return [];
        }
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

    public function saveReply(int $taskId, int $userId): bool
    {
        $reply = self::findOne(['task_id' => $taskId, 'contr_id' => $userId]);
        if (!$reply) {
            $reply = new Reply();
            $reply->comment = $this->comment;
            $reply->price = $this->price;
            $reply->task_id = $taskId;
            $reply->add_date = date("Y-m-d H:i:s");
            $reply->status = self::STATUS_PROPOSAL;
            $reply->contr_id = $userId;
            return $reply->save();
        }
        return false;
    }
}
