<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Category;
use app\models\Categories;
use app\models\TasksSelector;
use app\models\RepliesSelector;

class TasksController extends Controller
{
    private function renderTasks(Categories $categories, array $statuses)
    {
        $tasks = TasksSelector::selectTasks($categories, $statuses);
        $categoryNames[Categories::MAIN_CATEGORIES] = Category::getCategoryNames();
        $categoryNames[Categories::ADD_CONDITION] = 'Без исполнителя';
        $categoryNames[Categories::PERIODS] = array_map(
            function ($key, $value) {
                return $key . ' ' . $value;
            },
            array_keys(TasksSelector::TIME_PERIODS),
            array_values(TasksSelector::TIME_PERIODS)
        );
        return $this->render('index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'categoryNames' => $categoryNames,
        ]);
    }

    public function actionIndex()
    {
        $categories = new Categories();
        return $this->renderTasks($categories, [TasksSelector::STATUS_NEW]);
    }

    public function actionView(int $id)
    {
        $task = TasksSelector::selectTask($id);
        $replies = RepliesSelector::selectRepliesByTask($task->id);
        return $this->render('view', [
            'task' => $task,
            'replies' => $replies,
        ]);
    }

    public function actionCategory(int $id)
    {
        $categories = new Categories();
        $categories->categoriesCheckArray = [$id];
        return $this->renderTasks($categories, [TasksSelector::STATUS_NEW]);
    }
}
