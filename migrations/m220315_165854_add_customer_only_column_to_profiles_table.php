<?php

use yii\db\Migration;

/**
 * Class m220315_165854_add_customer_only_column_to_profiles_table
 */
class m220315_165854_add_customer_only_column_to_profiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profiles}}', 'customer_only', 'integer not null default 1');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profiles}}', 'customer_only');
    }
}
