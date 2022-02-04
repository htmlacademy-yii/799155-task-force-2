<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%tasks}}`.
 */
class m220130_221034_drop_loc_id_column_from_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%tasks}}', 'loc_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%tasks}}', 'loc_id', $this->integer());
    }
}
