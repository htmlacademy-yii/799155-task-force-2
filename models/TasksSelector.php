<?php

/**
 * Класс для обработки формы в views/tasks/index.php
 * @property string $street адрес задания
 * @property string $city город
 * @property string $category категория задания
 */

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use TaskForce\exception\TaskForceException;

const TASK_PER_PAGE = 5;

class TasksSelector extends Task
{
    public $category;
    public $files;
    public $city;
    public $district;
    public $street;
    public $longitude;
    public $latitude;
    public $city_id;
    public $code;
    public $timeout = false; //true, если задание выполняется и просрочено

    public const TIME_PERIODS = [
        '1' => 'час',
        '12' => 'часов',
        '24' => 'часа',
    ];

    public function rules()
    {
        $message = 'Поле не может быть пустым';
        return [
            [['name', 'category'], 'required', 'message' => $message],
            [['description', 'city', 'district', 'street', 'status'], 'string'],
            [['description', 'name', 'budget', 'deadline'], 'safe'],
            [['category', 'files', 'city', 'street', 'district', 'status'], 'safe'],
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
            ['city', 'validateTown', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['district', 'validateDistrict', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['street', 'validateStreet', 'skipOnEmpty' => true, 'skipOnError' => false],
            ['deadline', 'validateDeadline', 'skipOnEmpty' => true, 'skipOnError' => false],
        ];
    }

    public function validateTown($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->city)) {
                return;
            }
            $cities = array_values(City::getCityNames());
            $city = ucwords($this->city);
            if (!in_array($city, $cities)) {
                $this->addError($attribute, 'Такого города нет в БД');
                return;
            }
            $data = Location::getGeoData($city);
            $this->city_id = $data['id'];
            $this->longitude = $data['lon'];
            $this->latitude = $data['lat'];
            return;
        }
    }

    public function validateDistrict($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->city) and !empty($this->district)) {
                $this->addError($attribute, 'Укажите название города');
            }
        }
    }

    public function validateStreet($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->city and !empty($this->street))) {
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
     * Проверяет, просрочено ли задание
     * @return bool false, если не просрочено
     */
    public function checkTimeout()
    {
        $delta = strtotime($this->deadline) - time();
        return $delta > 0;
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
            'city' => 'Town',
            'district' => 'District',
            'street' => 'Street',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        if (empty($this->files)) {
            return true;
        }
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


    /**
     * Сохраняет задание в БД
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

    /**
     * Делает выборку новых заданий с учетом желаемых категорий и
     * времени появления заданий
     * @param Categories $categories сущность для выбора категорий в форме views/tasks/index.php
     *
     * @return array возвращает массив выбранных новых заданий
     */
    public static function selectTasks(
        Categories $categories,
        array $statuses,
        int $userId = 0,
        int $limit = TASK_PER_PAGE,
        $offset = 0
    ): array {
        $selectedCategoriesId = $categories->categoriesCheckArray;
        $additionalCondition = $categories->additionCategoryCheck;
        $period = '';
        $request = Yii::$app->request;
        if ($request->isPost) {
            $categories->load($request->post());
            if ($categories->categoriesCheckArray !== Categories::CATEGORIES_NOT_SELECTED) {
                $selectedCategoriesId = array_values($categories->categoriesCheckArray);
            }
            if ($categories->additionCategoryCheck !== Categories::NO_ADDITION_SELECTED) {
                $additionalCondition = $categories->additionCategoryCheck;
            }
            if ($categories->period != null) {
                $period = $categories->period;
            }
        }
        $query = self::find()->select(['id']);
        if ($userId > 0) {
            $query = $query->where(['or' , ['custom_id' => $userId], ['contr_id' => $userId]]);
        }
        if ($selectedCategoriesId === Categories::CATEGORIES_NOT_SELECTED) {
            if ($additionalCondition === Categories::NO_ADDITION_SELECTED) {
                $query = $query->where(['in' , 'tasks.status', $statuses]);
            } else {
                $query = $query->where(['in' , 'tasks.status', $statuses]);
                $query = $query->andWhere(['contr_id' => 0]);
            }
        } else {
            if ($additionalCondition === Categories::NO_ADDITION_SELECTED) {
                $query = $query->where(['in' , 'tasks.status', $statuses]);
                $query = $query->andWhere(['in', 'cat_id', $selectedCategoriesId]);
            } else {
                foreach ($selectedCategoriesId as $catId) {
                    $query = $query->where(['in' , 'tasks.status', $statuses]);
                    $query = $query->andWhere(['in', 'cat_id', $selectedCategoriesId]);
                    $query = $query->andWhere(['contr_id' => 0]);
                }
            }
        };
        if (strlen($period) > 0) {
            $hours = array_keys(self::TIME_PERIODS);
            $date = date("Y-m-d H:i:s", time() - 3600 * $hours[$period]);
            $query = $query->andWhere(['>', 'add_date', "$date"]);
        }
        $query = $query->limit($limit)->offset($offset)->orderBy(['add_date' => SORT_DESC]);
        $taskIds = $query->all();
        $tasks = [];
        foreach ($taskIds as $taskId) {
            $tasks[] = self::selectTask($taskId->id);
        }
        return $tasks;
    }

    /**
     * Делает выборку заданий с заданным исполнителем
     * @param int $userId id активного пользователя
     * @param array $statuses массив требуемых статусов заданий
     * @param int $limit количество задач в выборке
     * @param int $offset смещение в выборке
     *
     * @return array возвращает массив выбранных заданий
     */
    public static function selectTasksByStatus(
        int $userId,
        array $statuses,
        int $limit = 5,
        int $offset = 0
    ): array {
        $tasks = [];
        $requierdFields = [
            'tasks.id',
            'tasks.status',
            'tasks.name',
            'tasks.description',
            'budget',
            'tasks.add_date',
            'categories.name as category',
        ];
        $query = self::find()->select(['tasks.id']);
        $query->where(['or' , ['custom_id' => $userId], ['contr_id' => $userId]]);
        $query->andWhere(['in', 'tasks.status', $statuses]);
        if (in_array(Task::FILTER_TIMEOUT, $statuses)) {
            $query->andWhere(['<', 'deadline', 'NOW()']);
        }
        $query->limit($limit)->offset($offset);
        $query->orderBy(['add_date' => SORT_DESC]);
        $taskIds = $query->all();
        foreach ($taskIds as $taskId) {
            $loc = Location::findOne(['task_id' => $taskId]);
            $fields = array_merge($requierdFields, $loc ? ['cities.name as city'] : []);
            $query = self::find()->select($fields)->where(['tasks.id' => $taskId]);
            $query->innerJoin('categories', 'tasks.cat_id = categories.id');
            if ($loc) {
                $query->innerJoin('locations', 'tasks.id = locations.task_id')->
                innerJoin('cities', 'cities.id = locations.city_id');
            }
            $tasks[] = $query->one();
        }
        return $tasks;
    }

    /**
     * Возвращает задание с заданным id
     * @param int $taskId id задания
     *
     * @return object возвращает искомую сущность
     */
    public static function selectTask(int $taskId): object
    {
        $requierdFields = [
            'tasks.id',
            'tasks.name',
            'tasks.budget',
            'tasks.status',
            'tasks.description',
            'categories.name as category',
            'categories.code as code',
            'tasks.add_date as add_date',
            'tasks.deadline',
            'cat_id',
            'custom_id',
            'contr_id',
        ];
        $addFields = [
            'cities.name as city',
            'locations.street as street',
        ];
        $loc = Location::findOne(['task_id' => $taskId]);
        if ($loc) {
            $requierdFields = array_merge($requierdFields, $addFields);
        }
        $query = self::find()->select($requierdFields);
        $query->where(['tasks.id' => $taskId]);
        $query = $query->
            innerJoin('users', 'custom_id = users.id')->
            innerJoin('categories', 'cat_id = categories.id');
        if ($loc) {
            $query = $query->innerJoin('locations', 'tasks.id = locations.task_id')->
                innerJoin('cities', 'cities.id = locations.city_id');
        }
        $task = $query->one();
        if ($task === null) {
            throw new TaskForceException('Задание id = ' . $taskId . ' не найдено!');
        }
        return $task;
    }
}
