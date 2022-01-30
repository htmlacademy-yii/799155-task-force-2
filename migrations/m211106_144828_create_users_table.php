<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m211106_144828_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'email' => $this->string(64)->notNull()->unique(),
            'password' => $this->string(64)->notNull(),
            'add_date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'contractor' => $this->tinyInteger(3)->defaultValue(0)->comment('исполнитль или заказчик'),
            'city_id' => $this->integer()->defaultValue(0)->comment('id города'),
        ]);

        $this->createIndex(
            'user_ind',
            'users',
            'email'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
