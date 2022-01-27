<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cities}}`.
 */
class m211106_161636_create_cities_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(11, 8)->notNull(),
        ]);

        $this->createIndex(
            'city_ind',
            'cities',
            'name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cities}}');
    }
}
