<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property int $task_id
 * @property string $link
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'link', 'size'], 'required'],
            [['task_id', 'size'], 'integer'],
            [['link'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'link' => 'Link',
            'size' => 'Размер ресурса, Кб',
        ];
    }

    /**
     * Делает выборку документов для заанного id задачи
     * @param int $taskId id задания
     *
     * @return array возвращает массив выбранных документов
     */
    public static function selectDocuments(int $taskId): array
    {
        $docs = self::find()->select(['link', 'size'])->where(['task_id' => $taskId])->all();
        return $docs;
    }
}
