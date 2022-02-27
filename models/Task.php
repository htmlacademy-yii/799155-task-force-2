<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use TaskForce\exception\TaskForceException;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property int $custom_id заказчик
 * @property int $contr_id исполнитель
 * @property string $name
 * @property string|null $description
 * @property int $cat_id категория задания
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
    //задание просрочено
    public const STATUS_TIMEOUT = 'timeout';

    public const TASK_DESCR = [
        self::STATUS_NEW => 'Новое задание',
        self::STATUS_DONE => 'Задание выполнено',
        self::STATUS_CANCELED => 'Задание отменено',
        self::STATUS_ON_DEAL => 'Задание в работе',
        self::STATUS_REFUSED => 'Задание провалено',
    ];

    public const FILTER_NEW = 'new';
    public const FILTER_PROCESS = 'process';
    public const FILTER_CLOSED = 'closed';
    public const FILTER_TIMEOUT = 'timeout';

    public const TASK_STATUSES = [
        [
            self::FILTER_NEW => [self::STATUS_NEW],
            self::FILTER_PROCESS => [self::STATUS_ON_DEAL],
            self::FILTER_CLOSED => [self::STATUS_DONE, self::STATUS_CANCELED, self::STATUS_REFUSED],
        ],
        [
            self::FILTER_PROCESS => [self::STATUS_ON_DEAL],
            self::FILTER_TIMEOUT => [self::STATUS_TIMEOUT, self::STATUS_ON_DEAL],
            self::FILTER_CLOSED => [self::STATUS_DONE, self::STATUS_REFUSED],
        ],
    ];

    public const FILTER_LINKS = [
        [
            self::FILTER_NEW => ['Новые', 'Новые задания'],
            self::FILTER_PROCESS => ['В процессе', 'Выполняемые задания'],
            self::FILTER_CLOSED => ['Закрытые', 'Законченные задания'],
        ],
        [
            self::FILTER_PROCESS => ['В процессе', 'Выполняемые задания'],
            self::FILTER_TIMEOUT => ['Просроченные', 'Просроченные задания'],
            self::FILTER_CLOSED => ['Закрытые', 'Законченные задания'],
        ]
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
            [['description', 'status'], 'string'],
            [['description', 'name', 'budget', 'deadline'], 'safe'],
            ['name', 'string', 'max' => 256],
            [
                'budget',
                'compare',
                'compareValue' => 1,
                'operator' => '>=',
                'type' => 'number',
                'message' => 'Стоимость должна быть больше нуля'
            ],
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
            'name' => 'Сущность задания',
            'description' => 'Район',
            'cat_id' => 'Cat ID',
            'budget' => 'Стоимость работы',
            'add_date' => 'Add Date',
            'deadline' => 'Срок выполнения',
            'fin_date' => 'Fin Date',
            'status' => 'Status',
        ];
    }
}
