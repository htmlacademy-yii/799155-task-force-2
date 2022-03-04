<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Category;
use app\models\Categories;
use app\models\Reply;
use app\models\TasksSelector;
use app\models\RepliesSelector;
use app\models\ReviewsSelector;
use app\models\Document;
use app\models\Task;
use app\models\User;
use app\models\Location;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\Pagination;
use TaskForce\logic\Action;

class TasksController extends SecuredController
{
    private function renderTasks(Categories $categories, array $statuses, $page, int $userId = 0)
    {
        $pages = new Pagination();
        $pages->setPage($page - 1);
        $pages->pageSize = TasksSelector::TASKS_PER_PAGE;
        $tasks = TasksSelector::selectTasks($categories, $statuses, $pages, $userId);
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
            'pages' => $pages,
        ]);
    }

    public function actionIndex(int $page = 1)
    {
        $categories = new Categories();
        return $this->renderTasks($categories, [TasksSelector::STATUS_NEW], $page);
    }

    public function acceptReply($reply)
    {
        //меняем статус отклика на принято
        $model = Reply::findOne(['id' => $reply->id]);
        $model->status = Reply::STATUS_ACCEPTED;
        $model->update();
        //стартуем задание
        $task = Task::findOne(['id' => $reply->task_id]);
        if (Action::doAction(Action::ACTION_START, $task, $this->user->id)) {
            $task->contr_id = $model->contr_id;
            if ($task->update() === false) {
                throw new \Exception('Не удалось изменить данные задачи id ' . $task->id);
            }
        }
    }

    public function rejectReply($reply)
    {
        //меняем статус отклика на отказано
        $model = Reply::findOne(['id' => $reply->id]);
        $model->status = Reply::STATUS_REJECTED;
        $model->update();
    }

    public function actionView(int $id)
    {
        $task = TasksSelector::selectTask($id);
        $reply = new RepliesSelector();
        $review = new ReviewsSelector();
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('reply') === 'ok') {
                $reply->load(Yii::$app->request->post());
                if (!$reply->saveReply($task->id, $this->user->id)) {
                    $message = [
                        'post' => Yii::$app->request->post(),
                        'info' => Yii::$app->helpers->getFirstErrorString($reply),
                    ];
                    Yii::trace($message, 'controllers');
                    return $this->refresh();
                }
            }
            if (Yii::$app->request->post('refuse') === 'ok') {
                $reply->load(Yii::$app->request->post());
                return $this->actionRefuse($task->id, $reply);
            }
            if (Yii::$app->request->post('review') === 'ok') {
                $review->load(Yii::$app->request->post());
                if (!$review->saveReview($task->id, $this->user->id)) {
                    $message = [
                        'post' => Yii::$app->request->post(),
                        'info' => Yii::$app->helpers->getFirstErrorString($review),
                    ];
                    Yii::trace($message, 'controllers');
                    return $this->refresh();
                } else {
                    return $this->actionDone($task->id);
                }
            }
        }
        $geoCode = null;
        if (!empty($task->city)) {
            $geoCode = Location::getGeoLocation($task->id);
        }
        $replies = RepliesSelector::selectRepliesByTask($task->id);
        $docs = Document::selectDocuments($id);
        return $this->render('view', [
            'task' => $task,
            'replies' => $replies,
            'docs' => $docs,
            'reply' => $reply,
            'review' => $review,
            'geocode' => $geoCode,
        ]);
    }

    public function actionDownload(int $docId)
    {
        $doc = Document::findOne($docId);
        if ($doc) {
            return Yii::$app->response->sendFile(
                Yii::$app->basePath . '/web' . Yii::$app->params['uploadPath'] . $doc->fname, 
                $doc->doc, 
                [ 'inline' => false,]
            );
        }
        return $this->refresh();
    }

    public function actionCategory(int $id, int $page = 1)
    {
        $categories = new Categories();
        $categories->categoriesCheckArray = [$id];
        return $this->renderTasks($categories, [TasksSelector::STATUS_NEW], $page);
    }

    public function actionAddTask()
    {
        $categories = Category::getCategoryNames();
        $model = new TasksSelector();
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
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ($action->id === 'add-task') {
            if ($this->user->contractor === 1) {
                throw new ForbiddenHttpException('Создание заданий разрешено только заказчикам!');
            }
        }
        return true;
    }

    /**
     * Принять отклик исполнителя
     * @param int $id id отклика исполнителя
     */
    public function actionAccept(int $id)
    {
        $reply = Reply::findOne(['id' => $id]);
        $this->acceptReply($reply);
        return $this->actionView($reply->task_id);
    }

    /**
     * Отклонить отклик исполнителя
     * @param int $id id отклика исполнителя
     */
    public function actionReject(int $id)
    {
        $reply = Reply::findOne(['id' => $id]);
        $this->rejectReply($reply);
        return $this->actionView($reply->task_id);
    }

    /**
     * Выолнить отказ от задания
     * @param int $id id задания
     */
    public function actionRefuse(int $taskId, RepliesSelector $reply)
    {
        $task = Task::findOne($taskId);
        $contr = User::findOne($task->contr_id);
        if ($this->user->id !== $contr->id) {
            throw new ForbiddenHttpException('Вы не являетесь исполнителем этого задания!');
        }
        $model = Reply::findOne(['contr_id' => $task->contr_id]);
        $model->status = Reply::STATUS_REFUSED;
        $model->comment = $reply->comment;
        $model->update();
        if (Action::doAction(Action::ACTION_REFUSE, $task, $contr->id)) {
            if ($task->update() === false) {
                $message = [
                    'controller id' => 'refuse',
                    'task' => $task,
                    'info' => Yii::$app->helpers->getFirstErrorString($task),
                ];
                Yii::trace($message, 'controllers');
            }
        }
        return $this->refresh();
    }

    /**
     * Выполнить отмену задания
     * @param int $id id задания
     */
    public function actionCancel(int $id)
    {
        $task = Task::findOne($id);
        $custom = User::findOne($task->custom_id);
        if ($this->user->id !== $custom->id) {
            throw new ForbiddenHttpException('У Вас нет прав отменить это задание!');
        }

        if (Action::doAction(Action::ACTION_CANCEL, $task, $custom->id)) {
            if ($task->update() === false) {
                throw new \Exception('Не удалось изменить данные задачи id ' . $task->id);
            }
        }
        return $this->redirect(['/task/' . $id]);
    }

    /**
     * Отметить задание выполненным
     * @param int $id id задания
     */
    public function actionDone(int $id)
    {
        $task = Task::findOne($id);
        $custom = User::findOne($task->custom_id);
        if ($this->user->id !== $custom->id) {
            throw new ForbiddenHttpException('Вы не являетесь заказчиком этого задания!');
        }
        $reply = Reply::findOne(['task_id' => $id, 'status' => Reply::STATUS_ACCEPTED]);
        $task->budget = $reply->price;
        if (Action::doAction(Action::ACTION_COMPLETE, $task, $custom->id)) {
            if ($task->update() === false) {
                throw new \Exception('Не удалось изменить данные задачи id ' . $task->id);
            }
        }
        return $this->refresh();
    }

    public function actionMyTasks(string $code, int $page)
    {
        $codes = [
            Task::FILTER_NEW,
            Task::FILTER_PROCESS,
            Task::FILTER_CLOSED,
            Task::FILTER_TIMEOUT,
        ];
        if (!in_array($code, $codes)) {
            throw new NotFoundHttpException('Страница для ' . $code . ' не найдена');
        }
        $contr = $this->user->contractor;
        $contr = $this->user->contractor;
        $pages = new Pagination();
        $pages->pageSize = TasksSelector::TASKS_PER_PAGE;
        $tasks = TasksSelector::selectTasksByStatus(
            $this->user->id,
            Task::TASK_STATUSES[$contr][$code],
            $pages
        );
        $pages->setPage($page - 1);
        return $this->render('my-task', [
            'code' => $code,
            'contr' => $contr,
            'tasks' => $tasks,
            'pages' => $pages,
        ]);
    }
}
