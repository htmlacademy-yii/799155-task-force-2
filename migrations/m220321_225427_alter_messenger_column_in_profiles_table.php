<?php

use yii\db\Migration;

/**
 * Class m220321_225427_alter_messenger_column_in_profiles_table
 */
class m220321_225427_alter_messenger_column_in_profiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%profiles}}', 'messenger', 'string(64) null default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%profiles}}', 'messenger', 'string(32) null default null');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220321_225427_alter_messenger_column_in_profiles_table cannot be reverted.\n";

        return false;
    }
    */
}
