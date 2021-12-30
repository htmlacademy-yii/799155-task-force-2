<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property int $custom_id заказчик
 * @property int $contr_id исполнитель
 * @property string $name
 * @property string|null $description
 * @property int $cat_id категория задания
 * @property int $loc_id локация задания
 * @property int $budget
 * @property string $add_date
 * @property string $deadline срок выполнения задания
 * @property string $fin_date фактический срок выполнения задания
 * @property string $status
 */
class Task extends ActiveRecord
{
    //новое задание
    public const STATUS_NEW = 'new';
    //задание выполнено
    public const STATUS_DONE = 'done';
    //задание отменено
    public const STATUS_CANCELED = 'canceled';
    //задание в работе
    public const STATUS_ON_DEAL = 'on_deal';
    //задание провалено
    public const STATUS_REFUSED = 'refused';

    public const TASK_DESCR = [
        self::STATUS_NEW => 'Новое задание',
        self::STATUS_DONE => 'Задание выполнено',
        self::STATUS_CANCELED => 'Задание отменено',
        self::STATUS_ON_DEAL => 'Задание в работе',
        self::STATUS_REFUSED => 'Задание провалено',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['custom_id', 'contr_id', 'name', 'cat_id', 'loc_id',
                'budget', 'add_date', 'deadline', 'fin_date', 'status'], 'required'],
            [['custom_id', 'contr_id', 'cat_id', 'loc_id', 'budget'], 'integer'],
            [['description'], 'string'],
            [['add_date', 'deadline', 'fin_date'], 'safe'],
            [['add_date', 'deadline', 'fin_date'], 'date'],
            [['name'], 'string', 'max' => 256],
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
            'custom_id' => 'Custom ID',
            'contr_id' => 'Contr ID',
            'name' => 'Name',
            'description' => 'Description',
            'cat_id' => 'Cat ID',
            'loc_id' => 'Loc ID',
            'budget' => 'Budget',
            'add_date' => 'Add Date',
            'deadline' => 'Deadline',
            'fin_date' => 'Fin Date',
            'status' => 'Status',
        ];
    }
}
