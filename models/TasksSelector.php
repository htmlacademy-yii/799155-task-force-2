<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Task;
use app\models\Category;
use yii\web\Controller;
use app\models\Categories;
use yii\helpers\ArrayHelper;

/**
 * Класс модели для обработки формы views/tasks/index.php
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

class TasksSelector extends Task
{
    public const SELECT_PERIODS = [
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
            $hours = array_keys(self::SELECT_PERIODS);
            $date = date("Y-m-d H:i:s", time() - 3600 * $hours[$period]);
            $query = $query->andWhere(['>', 'add_date', "$date"]);
        }
        $query = $query->limit(3)->offset(0)->orderBy(['add_date' => SORT_DESC]);
        $tasks = $query->all();
        return $tasks;
    }
}
