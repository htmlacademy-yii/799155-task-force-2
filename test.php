<?php

use TaskForce\logic\Task;
use TaskForce\logic\ActionStart;
use TaskForce\logic\ActionComplete;
use TaskForce\logic\ActionCancel;
use TaskForce\logic\ActionRefuse;

require_once 'vendor/autoload.php';

/**
 * Проверка ожидаемого результата
 *
 * @param mixed  $result   некоторое значение переменной
 * @param mixed  $expected ожидаемое значение переменной
 * @param string $msg      сообщение в случае несовпадения значений
 */
function assertTest($result, $expected, string $msg): void
{
    try {
        if (is_array($result) and is_array($expected)) {
            assert(count($result) == count($expected));
            $map = array_map(function ($res, $exp) {
                if (is_object($res) and is_object($exp)) {
                    return get_class($res) == get_class($exp);
                } elseif (!is_object($res) and !is_object($exp)) {
                    return $res === $exp;
                }
                return false;
            }, $result, $expected);
            assert(array_reduce($map, function ($out, $value) {
                $out &= $value;
                return $out;
            }, true), $msg);
        } else {
            assert($result === $expected, $msg);
        }
    } catch (Error $e) {
        echo $e->getMessage(), \PHP_EOL;
    }
}

// настройка assert
assert_options(\ASSERT_ACTIVE, 1);
function assertMessage($file, $line, $code = null, $desc = null)
{
    echo ($desc ? "$desc - " : '') . " line" . $line . "File $file" . \PHP_EOL;
}
assert_options(\ASSERT_CALLBACK, 'assertMessage');

$task = new Task(1, 23);
$statusMap = $task->getStatusMap();
$result = [
    'new' => 'новое задание',
    'canceled' => 'задание отменено',
    'done' => 'задание завершено',
    'failed' => 'задание провалено',
    'work' => 'задание в работе'
];
assertTest($result, $statusMap, 'Unexpected statuses map');

$actionMap = $task->getActionMap();
$result = [
    'start' => 'стартовать задание',
    'complete' => 'завершить задание',
    'refuse' => 'отказаться от задания',
    'cancel' => 'отменить задание'
];

assertTest($result, $actionMap, 'Unexpected actions map');

$actStart = new ActionStart();
$actComplete = new ActionComplete();
$actCancel = new ActionCancel();
$actRefuse = new ActionRefuse();

try {
    $result = $task->mapActionToStatus($actStart);
    $expected = Task::STATUS_WORK;
    assertTest($result, $expected, 'Unexpected status for action ' . $actStart->getName());

    $result = $task->mapActionToStatus($actComplete);
    $expected = Task::STATUS_DONE;
    assertTest($result, $expected, 'Unexpected status for action ' . $actComplete->getName());

    $result = $task->mapActionToStatus($actRefuse);
    $expected = Task::STATUS_FAILED;
    assertTest($result, $expected, 'Unexpected status for action ' . $actRefuse->getName());

    $result = $task->mapActionToStatus($actCancel);
    $expected = Task::STATUS_CANCELED;
    assertTest($result, $expected, 'Unexpected status for action ' . $actCancel->getName());

    //пользователь - заказчик, проверка доступных действий
    $result = $task->mapStatusToAllowedActions(Task::STATUS_NEW, 1);
    $expected = [new ActionStart(), new ActionCancel()];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_NEW);

    //пользователь - исполнитель, проверка доступных действий
    $result = $task->mapStatusToAllowedActions(Task::STATUS_NEW, 23);
    $expected = [];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_NEW);

    //пользователь - заказчик, проверка доступных действий
    $result = $task->mapStatusToAllowedActions(Task::STATUS_WORK, 1);
    $expected = [new ActionComplete()];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_WORK);

    //пользователь - исполнитель, проверка доступных действий
    $result = $task->mapStatusToAllowedActions(Task::STATUS_WORK, 23);
    $expected = [new ActionRefuse()];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_WORK);

    $result = $task->mapStatusToAllowedActions(Task::STATUS_CANCELED, 1);
    $expected = [];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_CANCELED);

    $result = $task->mapStatusToAllowedActions(Task::STATUS_DONE, 1);
    $expected = [];
    assertTest($result, $expected, 'Unexpected map of actions for status ' . Task::STATUS_DONE);

    $result = $task->mapStatusToAllowedActions(Task::STATUS_FAILED, 1);
    $expected = [];
    assertTest($result, $expected, 'Unexpected map of actions' . Task::STATUS_FAILED);
} catch (TaskForceException $e) {
    echo $e->getMessage() . \PHP_EOL;
} catch (TaskForceActionException $e) {
    echo $e->getMessage() . \PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . \PHP_EOL;
}
