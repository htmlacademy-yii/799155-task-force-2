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
    public $category;
    public $files;
    public $town;
    public $district;
    public $street;
    public $longitude;
    public $latitude;
    public $city_id;

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
        $message = 'Поле не может быть пустым';
        return [
            [['name', 'category'], 'required', 'message' => $message],
            [['description', 'town', 'district', 'street'], 'string'],
            [['description', 'name', 'budget', 'deadline','category', 'files', 'town', 'street', 'district'], 'safe'],
            ['name', 'string', 'max' => 256],
            ['files', 'file', 'extensions' => 'doc, docx, txt', 'maxFiles' => 2],
            [
                'budget',
                'compare',
                'compareValue' => 1,
                'operator' => '>=',
                'type' => 'number',
                'message' => 'Стоимость должна быть больше нуля'
            ],
            ['category', 'string' , 'message' => $message],
            ['town', 'validateTown', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['district', 'validateDistrict', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['street', 'validateStreet', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['deadline', 'validateDeadline', 'skipOnEmpty' => true, 'skipOnError' => false],
        ];
    }

    public function validateTown($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->town)) {
                return;
            }
            $cities = array_values(City::getCityNames());
            $town = ucwords($this->town);
            if (!in_array($town, $cities)) {
                $this->addError($attribute, 'Такого города нет в БД');
                return;
            }
            $city = Location::getGeoData($town);
            $this->city_id = $city['id'];
            $this->longitude = $city['lon'];
            $this->latitude = $city['lat'];
            return;
        }
    }

    public function validateDistrict($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->town) and !empty($this->district)) {
                $this->addError($attribute, 'Укажите название города');
            }
        }
    }

    public function validateStreet($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->town and !empty($this->street))) {
                $this->addError($attribute, 'Укажите название города');
            }
        }
    }

    public function validateDeadline($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->deadline)) {
                return;
            }
            $delta = strtotime($this->deadline) - time();
            if ($delta < 24 * 60 * 60) {
                $this->addError($attribute, 'Дата не может быть раньше текущего дня');
            }
        }
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
            'category' => 'Категория',
            'files' => 'Доп. файлы',
            'town' => 'Town',
            'district' => 'District',
            'street' => 'Street',
        ];
    }

    /**
     * Сохраняет задагие в БД
     * @return bool результат операции соохранения в БД
     */
    public function saveTask(): bool
    {
        $this->custom_id = Yii::$app->user->getId();
        $this->add_date = date("Y-m-d H:i:s");
        $this->status = Task::STATUS_NEW;
        if ($this->save()) {
            if (Location::saveLocation($this)) {
                if (Document::saveDocuments($this)) {
                    return true;
                }
                //удаляем запись локации
                $loc = Location::findOne(['task_id' => $this->id]);
                if ($loc) {
                    $loc->delete();
                }
            }
            //удаляем запись задания
            $this->delete();
        }
        return false;
    }

    public function beforeValidate(): bool
    {
        $mimeTypes = [
            'application/msword',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        foreach ($this->files as $file) {
            if (!Yii::$app->helpers->validateUploadedFile($file, $mimeTypes)) {
                return false;
            }
        }
        return true;
    }
}
