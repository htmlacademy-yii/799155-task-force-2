<?php

namespace TaskForce\logic;

use app\models\Task;

class Action
{
    public const ACTION_START = 'start';
    public const ACTION_CANCEL = 'cancel';
    public const ACTION_COMPLETE = 'complete';
    public const ACTION_REFUSE = 'refuse';

    public const ACTION_STATUS_MAP = [
        self::ACTION_START => Task::STATUS_ON_DEAL,
        self::ACTION_COMPLETE => Task::STATUS_DONE,
        self::ACTION_REFUSE => Task::STATUS_REFUSED,
        self::ACTION_CANCEL => Task::STATUS_CANCELED
    ];

    public const ALLOWED_ACTIONS = [
        Task::STATUS_NEW => [self::ACTION_START, self::ACTION_CANCEL],
        Task::STATUS_ON_DEAL => [self::ACTION_COMPLETE, self::ACTION_REFUSE],
        Task::STATUS_CANCELED => [],
        Task::STATUS_DONE => [],
        Task::STATUS_REFUSED => []
    ];

    /**
     * Возвращает значение статуса, в которой перейдёт заданме
     * после выполнения указанного действия
     *
     * @param string $action требуемое действие
     *
     * @return string значение статуса задания, соответсвующего действию
     * или null, если такого статуса нет
     */
    public static function mapActionToStatus(string $action): ?string
    {
        return self::ACTION_STATUS_MAP[$action] ?? null;
    }

    /**
     * Возвращает массив доступных действий, соответствующий заданному статусу задания
     *
     * @param string $status - заданный статус
     *
     * @return array - массив доступных действий
     * или пустой массив, если доступных действий нет
     */
    public static function mapStatusToAllowedActions(string $status): array
    {
        return self::ALLOWED_ACTIONS[$status] ?? [];
    }

    /**
     * Дает заданию новый статус в соответсвии с действием
     *
     * @param string $action действие
     * @param Task $task задание
     * @param int $userId id аторизованного пользователя
     *
     * @return bool true если действие было выполнено
     * или false если действие недоступно
     */
    public static function doAction(string $action, Task $task, int $userId): bool
    {
        if (
            ($action === self::ACTION_REFUSE and $userId === $task->contr_id) or
            $userId === $task->custom_id
        ) {
            $actions = self::mapStatusToAllowedActions($task->status);
            if (count($actions) === 0) {
                return false;
            }
            if (array_search($action, $actions) !== false) {
                $nextStatus = self::mapActionToStatus($action);
                if ($nextStatus !== null) {
                    $task->status = $nextStatus;
                    return true;
                }
            }
        }
        return false;
    }
}
