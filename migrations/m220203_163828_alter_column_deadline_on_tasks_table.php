<?php

use yii\db\Migration;

/**
 * Class m220203_163828_alter_column_deadline_on_tasks_table
 */
class m220203_163828_alter_column_deadline_on_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%tasks}}', 'deadline', 'date default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%tasks}}', 'deadline', 'date');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }

    public function down()
    {
    }
    */
}
