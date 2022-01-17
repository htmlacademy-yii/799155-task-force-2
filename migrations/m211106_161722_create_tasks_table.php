<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tasks}}`.
 */
class m211106_161722_create_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(),
            'custom_id' => $this->integer()->notNull()->comment('заказчик'),
            'contr_id' => $this->integer()->defaultValue(0)->comment('исполнитель'),
            'name' => $this->string(256)->notNull(),
            'description' => $this->text()->null(),
            'cat_id' => $this->integer()->notNull()->comment('категория задания'),
            'loc_id' => $this->integer()->notNull()->comment('локация задания'),
            'budget' => $this->integer()->notNull(),
            'add_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'deadline' => $this->dateTime()->notNull()->comment('срок выполнения задания'),
            'fin_date' => $this->dateTime()->null()->comment('фактический срок выполнения задания'),
            'status' => $this->string('16')->notNull()->defaultValue('new'),
        ])->comment('Таблица заданий');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tasks}}');
    }
}
