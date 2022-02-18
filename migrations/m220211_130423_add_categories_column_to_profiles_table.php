<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%profiles}}`.
 */
class m220211_130423_add_categories_column_to_profiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profiles}}', 'categories', 'string default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profiles}}', 'categories');
    }
}
