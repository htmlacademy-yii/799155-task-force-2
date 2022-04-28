<?php

use yii\db\Migration;

/**
 * Заполняет таблицу городов cities
 */
class m220427_172849_fill_cities_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents(Yii::$app->basePath . '/data/sql/cities.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%cities}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_172849_fill_cities_table cannot be reverted.\n";

        return false;
    }
    */
}
