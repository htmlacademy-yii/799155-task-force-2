<?php

use yii\db\Migration;

/**
 * Заполняет таблицу категорий categories
 */
class m220427_213551_fill_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents(Yii::$app->basePath . '/data/sql/categories.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%categories}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_213551_fill_categories_table cannot be reverted.\n";

        return false;
    }
    */
}
