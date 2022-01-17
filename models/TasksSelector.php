<?php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Categories;
use yii\helpers\ArrayHelper;
use TaskForce\exception\TaskForceException;

/**
 * Класс для обработки формы в views/tasks/index.php
 * @property string $street адрес задания
 * @property string $city город
 * @property string $category категория задания
 */

class TasksSelector extends Task
{

    public $street;
    public $city;
    public $category;
    public $code;

    public const TIME_PERIODS = [
        '1' => 'час',
        '12' => 'часов',
        '24' => 'часа',
    ];

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
        int $limit = 3,
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
        $query = self::find()->select([
            'tasks.id',
            'tasks.name',
            'tasks.budget',
            'description',
            'tasks.cat_id',
            'cities.name as city',
            'locations.street as street',
            'categories.name as category',
            'categories.code as code',
            'add_date',
        ]);
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
        }
        $query = $query->
            innerJoin('locations', 'loc_id = locations.id')->
            innerJoin('categories', 'cat_id = categories.id')->
            innerJoin('cities', 'cities.id = locations.city_id');
        if (strlen($period) > 0) {
            $hours = array_keys(self::TIME_PERIODS);
            $date = date("Y-m-d H:i:s", time() - 3600 * $hours[$period]);
            $query = $query->andWhere(['>', 'add_date', "$date"]);
        }
        $query = $query->limit($limit)->offset($offset)->orderBy(['add_date' => SORT_DESC]);
        $tasks = $query->all();
        return $tasks;
    }

    /**
     * Делает выборку заданий с заданным исполнителем
     * @param $contractor исполнитель задания
     * @param string|null $taskStatuses массив требуемых статусов заданий
     * @param int|null $limit требуемый статус задания
     *
     * @return array возвращает массив выбранных заданий
     */
    public static function selectTasksByContractor(
        int $contractor,
        array $taskStatuses = null,
        int $limit = null
    ): array {
        $query = self::find()->select([
            'id',
            'status',
            'name',
            'add_date',
        ]);
        $query->where(['tasks.contr_id' => $contractor]);
        if ($taskStatuses and count($taskStatuses)) {
            $query = $query->andWhere(['in', 'tasks.status', $taskStatuses]);
        }
        if ($limit) {
            $query = $query->limit($limit)->offset(0);
        }
        $query = $query->orderBy(['add_date' => SORT_DESC]);
        $tasks = $query->all();
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
        $query = self::find()->select([
            'tasks.id',
            'tasks.name',
            'tasks.budget',
            'tasks.status',
            'tasks.description',
            'cities.name as city',
            'locations.street as street',
            'categories.name as category',
            'tasks.add_date as add_date',
            'tasks.deadline',
            'cat_id',
        ]);
        $query->where(['tasks.id' => $taskId]);
        $query = $query->
            innerJoin('users', 'custom_id = users.id')->
            innerJoin('locations', 'loc_id = locations.id')->
            innerJoin('categories', 'cat_id = categories.id')->
            innerJoin('cities', 'cities.id = locations.city_id');
        $task = $query->one();
        if ($task === null) {
            throw new TaskForceException('Задание id = ' . $taskId . ' не найдено!');
        }
        return $task;
    }
}
