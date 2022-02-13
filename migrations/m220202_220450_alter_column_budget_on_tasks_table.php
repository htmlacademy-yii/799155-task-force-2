<?php

use yii\db\Migration;

/**
 * Class m220202_220450_alter_column_budget_on_tasks_table
 */
class m220202_220450_alter_column_budget_on_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%tasks}}', 'budget', 'integer default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%tasks}}', 'budget', 'integer');
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
