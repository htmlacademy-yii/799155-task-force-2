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
 * @property int $rating
 * @property string $status
 */
class Reply extends \yii\db\ActiveRecord
{
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

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
            [['task_id', 'contr_id', 'price', 'add_date', 'rating', 'status'], 'required'],
            [['task_id', 'contr_id', 'price', 'rating'], 'integer'],
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
            'price' => 'Price',
            'comment' => 'Comment',
            'add_date' => 'Add Date',
            'rating' => 'Rating',
            'status' => 'Status',
        ];
    }
}
