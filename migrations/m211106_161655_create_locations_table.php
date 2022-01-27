<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%locations}}`.
 */
class m211106_161655_create_locations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%locations}}', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull()->comment('широта места'),
            'longitude' => $this->decimal(11, 8)->notNull()->comment('долгота места'),
            'district' => $this->string(64)->null()->comment('район'),
            'street' => $this->string(64)->null()->comment('улица'),
            'info' => $this->text()->null()->comment('дополн. информация'),
        ]);

        $this->createIndex(
            'loc_ind',
            'locations',
            'city_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%locations}}');
    }
}
