<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%locations}}`.
 */
class m220130_220653_add_task_id_column_to_locations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%locations}}', 'task_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%locations}}', 'task_id');
    }
}
