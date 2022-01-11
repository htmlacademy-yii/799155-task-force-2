<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property int $task_id
 * @property int $custom_id заказчик работы
 * @property int $contr_id исполнитель работы
 * @property string $add_date
 * @property string $comment отзыв заказчика о работе
 * @property int $rating
 */
class Review extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'custom_id', 'contr_id', 'add_date', 'comment', 'rating'], 'required'],
            [['task_id', 'custom_id', 'contr_id', 'rating'], 'integer'],
            [['add_date'], 'safe'],
            [['comment'], 'string'],
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
            'custom_id' => 'Custom ID',
            'contr_id' => 'Contr ID',
            'add_date' => 'Add Date',
            'comment' => 'Comment',
            'rating' => 'Rating',
        ];
    }
}
