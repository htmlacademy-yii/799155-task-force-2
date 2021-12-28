<?php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Categories;
use yii\helpers\ArrayHelper;

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

    public const TIME_PERIODS = [
        '1' => 'час',
        '12' => 'часов',
        '24' => 'часа',
    ];

    /**
     * Делает выборку новых заданий с учетом желаемых категорий и
     * времени появления заданий
     * @param Categories $categories сущность для выбора категорий в форме views/tasks/index.php
     * @return array возвращает массив выбранных новых заданий
     */
    public static function selectNewTasks($categories): array
    {
        $request = Yii::$app->request;
        $selectedCategoriesId = Categories::CATEGORIES_NOT_SELECTED;
        $additionalCondition = Categories::NO_ADDITION_SELECTED;
        $period = '';
        if ($request->isPost) {
            $categories->load(Yii::$app->request->post());
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
            'tasks.name',
            'tasks.budget',
            'cities.name as city',
            'locations.street as street',
            'categories.name as category',
            'add_date',
        ]);
        if ($selectedCategoriesId === Categories::CATEGORIES_NOT_SELECTED) {
            if ($additionalCondition === Categories::NO_ADDITION_SELECTED) {
                $query = $query->where(['tasks.status' => 'new']);
            } else {
                $query = $query->where(['tasks.status' => 'new', 'contr_id' => 0]);
            }
        } else {
            if ($additionalCondition === Categories::NO_ADDITION_SELECTED) {
                foreach ($selectedCategoriesId as $catId) {
                    $query = $query->orWhere(['tasks.status' => 'new', 'cat_id' => "$catId"]);
                }
            } else {
                foreach ($selectedCategoriesId as $catId) {
                    $query = $query->orWhere(
                        [
                            'tasks.status' => 'new',
                            'contr_id' => 0,
                            'cat_id' => "$catId"
                        ]
                    );
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
        $query = $query->limit(3)->offset(0)->orderBy(['add_date' => SORT_DESC]);
        $tasks = $query->all();
        return $tasks;
    }
}
