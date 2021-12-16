<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\models\Categories;
use app\models\TasksSelector;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $categories = new Categories();
        $tasks = TasksSelector::selectNewTasks($categories);
        $cats = Category::find()->select("*")->all();
        $categoryNames[Categories::MAIN_CATEGORIES] = ArrayHelper::map($cats, 'id', 'name');
        $categoryNames[Categories::ADD_CONDITION] = 'Без исполнителя';
        $categoryNames[Categories::PERIOD] = array_map(
            function ($key, $value) {
                return $key . ' ' . $value;
            },
            array_keys(TasksSelector::SELECT_PERIODS),
            array_values(TasksSelector::SELECT_PERIODS)
        );
        return $this->render('index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'categoryNames' => $categoryNames,
        ]);
    }
}
