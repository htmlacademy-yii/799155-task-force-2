<?php

namespace app\models;

use Yii;
use yii\web\UnsupportedMediaTypeHttpException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property int $task_id
 * @property string $doc
 * @property string $fname
 */
class Document extends ActiveRecord
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
            [['task_id', 'doc', 'size'], 'required'],
            [['task_id', 'size'], 'integer'],
            [['doc'], 'string', 'max' => 512],
            [['fname'], 'string', 'max' => 255],
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
            'doc' => 'Имя файла',
            'size' => 'Размер ресурса, Кб',
            'fname' => 'Имя в папке uploads',
        ];
    }

    /**
     * Делает выборку документов для заданного id задачи
     * @param int $taskId id задания
     *
     * @return array возвращает массив выбранных документов
     */
    public static function selectDocuments(int $taskId): array
    {
        $docs = self::find()->select(['id', 'doc', 'size'])->where(['task_id' => $taskId])->all();
        return $docs;
    }

    /**
     * Сохраняет информацию о дополнительных документах задания
     * @param ActiveRecord $model задание
     * @return bool результат операции соохранения в БД
    */
    public static function saveDocuments(ActiveRecord $model): bool
    {
        foreach ($model->files as $file) {
            $newName = uniqid('up') . '.' . $file->extension;
            $props = [
                'task_id' => $model->id,
                'doc' => $file->baseName . '.' . $file->extension,
                'size' => ceil($file->size / 1024),
                'fname' => $newName,
            ];
            $doc = new Document();
            $doc->attributes = $props;
            if ($doc->save() === false) {
                return false;
            }
            move_uploaded_file(
                $file->tempName,
                Yii::$app->basePath . Yii::$app->params['uploadPath'] . $newName
            );
        }
        return true;
    }
}
