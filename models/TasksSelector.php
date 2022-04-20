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
use yii\data\Pagination;
use yii\db\Query;

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
    public $address;
    public $count;
    public $location;

    public const TASKS_PER_PAGE = 5;

    public const TIME_PERIODS = [
        '1' => 'час',
        '12' => 'часов',
        '24' => 'часа',
    ];

    public function rules()
    {
        $message = 'Поле не может быть пустым';
        return [
            [['name', 'category', 'address'], 'required', 'message' => $message],
            [['description', 'city', 'district', 'street', 'status'], 'string'],
            [['description', 'name', 'budget', 'deadline'], 'safe'],
            [['category', 'files', 'address', 'status', 'city', 'street'], 'safe'],
            [['longitude', 'latitude'], 'safe'],
            [['longitude', 'latitude'], 'validateAddress'],
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
            ['deadline', 'validateDeadline', 'skipOnEmpty' => true, 'skipOnError' => false],
        ];
    }

    public function validateAddress($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->longitude) or empty($this->latitude)) {
                $this->addError($attribute, 'Ошибка в задании локации');
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
            'address' => 'Адрес задания',
            'city' => 'Город',
            'street' => 'Улица',
            'count' => 'Задания без откликов',
            'loc_id' => 'ID локации задания',
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
        if ($this->save() === true) {
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
        Pagination $pages,
        int $userId = 0
    ): array {
        $selectedCategoriesId = $categories->categoriesCheckArray;
        $additionalCondition = $categories->additionCategoryCheck;
        $remoteTaskCondition = $categories->moreConditionCheck;
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
            if ($categories->moreConditionCheck !== Categories::NO_ADDITION_SELECTED) {
                $remoteTaskCondition = $categories->moreConditionCheck;
            }
            if ($categories->period != null) {
                $period = $categories->period;
            }
        }
        $query = null;
        if ($remoteTaskCondition !== Categories::NO_ADDITION_SELECTED) {
            $query = (new Query())->select(
                [
                    'tasks.id',
                    'cat_id',
                    'tasks.custom_id',
                    'tasks.contr_id',
                    'tasks.add_date',
                    'tasks.status',
                    'city_id'
                ])->from('tasks')->
                leftJoin('locations l', 'tasks.id = l.task_id')->where(['city_id' => null]);
        }
        if ($additionalCondition !== Categories::NO_ADDITION_SELECTED) {
            if ($query === null) {
                $query = (new Query())->select(['tasks.id', 'reviews'])->from('tasks')->
                    leftJoin('replies r', 'tasks.id = r.task_id')->where(['reviews' => null]);
            } else {
                $query = $query->select(['reviews'])->
                    leftJoin('replies r', 'tasks.id = r.task_id')->where(['reviews' => null]);
            }
        }
        if ($query === null) {
            $query = (new Query())->select(
                [
                    'id',
                    'cat_id',
                    'custom_id',
                    'contr_id',
                    'add_date',
                    'status',
                ])->from('tasks');
        }
        if ($userId > 0) {
            $query = $query->andWhere(['or' , ['tasks.custom_id' => $userId], ['tasks.contr_id' => $userId]]);
        }
        $query = $query->andWhere(['in' , 'tasks.status', $statuses]);
        if ($selectedCategoriesId !== Categories::CATEGORIES_NOT_SELECTED) {
            $query = $query->andWhere(['in', 'cat_id', $selectedCategoriesId]);
        };
        if (strlen($period) > 0) {
            $hours = array_keys(self::TIME_PERIODS);
            $date = date("Y-m-d H:i:s", time() - 3600 * $hours[$period]);
            $query = $query->andWhere(['>', 'tasks.add_date', "$date"]);
        }
        $query = $query->orderBy(['tasks.add_date' => SORT_DESC]);
        $countQuery = clone $query;
        $pages->totalCount = count($countQuery->all());
        $pages->forcePageParam = false;
        $pages->pageSizeParam = false;
        $query = $query->limit($pages->limit)->offset($pages->offset)->
                orderBy(['tasks.add_date' => SORT_DESC]);
        $taskIds = $query->all();
        $tasks = [];
        foreach ($taskIds as $taskId) {
            if (isset($taskId['id'])) {
                $tasks[] = self::selectTask($taskId['id']);
            }
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
        Pagination $pages
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
            'custom_id'
        ];
        $query = self::find()->select(['tasks.id']);
        $query->where(['or' , ['custom_id' => $userId], ['contr_id' => $userId]]);
        $query->andWhere(['in', 'tasks.status', $statuses]);
        if (in_array(Task::FILTER_TIMEOUT, $statuses)) {
            $query->andWhere(['<', 'deadline', 'NOW()']);
        }
        $countQuery = clone $query;
        $pages->totalCount = $countQuery->count();
        $pages->pageSize = self::TASKS_PER_PAGE;
        $pages->forcePageParam = false;
        $pages->pageSizeParam = false;
        $query->limit($pages->limit)->offset($pages->offset);
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
            $message = 'Задание id = ' . $taskId . ' не найдено!';
            Yii::$app->getSession()->setFlash('error', $message);
        }
        return $task;
    }
}
