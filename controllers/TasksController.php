<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Category;
use app\models\Categories;
use app\models\TasksSelector;
use app\models\RepliesSelector;
use app\models\Document;
use app\models\Task;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

class TasksController extends SecuredController
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
        $docs = Document::selectDocuments($id);
        return $this->render('view', [
            'task' => $task,
            'replies' => $replies,
            'docs' => $docs,
        ]);
    }

    public function actionCategory(int $id)
    {
        $categories = new Categories();
        $categories->categoriesCheckArray = [$id];
        return $this->renderTasks($categories, [TasksSelector::STATUS_NEW]);
    }

    public function actionAddTask()
    {
        $categories = Category::getCategoryNames();
        $model = new Task();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->cat_id = Category::getId($categories[$model->category]);
            $model->files = UploadedFile::getInstances($model, 'files');
            if ($model->validate()) {
                if ($model->saveTask()) {
                    return $this->redirect(['/task/' . $model->id]);
                }
            }
        }
        return $this->render('add-task', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    public function beforeAction($action)
    {
        if ($action->id === 'add-task') {
            $user = Yii::$app->helpers->checkAuthorization();
            if ($user->contractor === 1) {
                throw new ForbiddenHttpException('Создание заданий разрешено только заказчикам!');
            }
        }
        return parent::beforeAction($action);
    }
}
