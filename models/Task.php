<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
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
class Task extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['custom_id', 'contr_id', 'name', 'cat_id', 'loc_id',
                'budget', 'add_date', 'deadline', 'fin_date', 'status'], 'required'],
            [['custom_id', 'contr_id', 'cat_id', 'loc_id', 'budget'], 'integer'],
            [['description'], 'string'],
            [['add_date', 'deadline', 'fin_date'], 'safe'],
            [['add_date', 'deadline', 'fin_date'], 'date'],
            [['name'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'custom_id' => 'Custom ID',
            'contr_id' => 'Contr ID',
            'name' => 'Name',
            'description' => 'Description',
            'cat_id' => 'Cat ID',
            'loc_id' => 'Loc ID',
            'budget' => 'Budget',
            'add_date' => 'Add Date',
            'deadline' => 'Deadline',
            'fin_date' => 'Fin Date',
            'status' => 'Status',
        ];
    }
}
