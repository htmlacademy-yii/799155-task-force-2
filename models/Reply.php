<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "replies".
 *
 * @property int $id
 * @property int $task_id
 * @property int $contr_id
 * @property int $price
 * @property string|null $comment
 * @property string $add_date
 * @property int $reviews
 * @property string $status
 */
class Reply extends \yii\db\ActiveRecord
{
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROPOSAL = 'proposal';
    public const STATUS_REFUSED = 'refused';

    public $rating = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'replies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'contr_id', 'price', 'add_date', 'status'], 'required'],
            [['task_id', 'contr_id', 'price'], 'integer'],
            [['comment'], 'string'],
            [['add_date'], 'safe'],
            [['status'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'contr_id' => 'Contr ID',
            'price' => 'Перлагаемая цена услуги',
            'comment' => 'Дополнительная информация',
            'add_date' => 'Add Date',
            'reviews' => 'Reviews count',
            'status' => 'Status',
        ];
    }

    public function saveReply(int $taskId, int $userId): bool
    {
        $this->task_id = $taskId;
        $this->add_date = date("Y-m-d H:i:s");
        $this->status = self::STATUS_PROPOSAL;
        $this->contr_id = $userId;
        return $this->save();
    }
}
