<?php

use yii\db\Migration;

/**
 * Class m220206_175837_rename_rating_column_to_reviews_in_replies_table
 */
class m220206_175837_rename_rating_column_to_reviews_in_replies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%replies}}', 'rating', 'reviews');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%replies}}', 'reviews', 'rating');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220206_175837_change_name_rating_column_to_reviews_on_replies_table cannot be reverted.\n";

        return false;
    }
    */
}
