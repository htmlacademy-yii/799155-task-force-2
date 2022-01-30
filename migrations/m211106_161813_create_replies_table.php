<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%replies}}`.
 */
class m211106_161813_create_replies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%replies}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'contr_id' => $this->integer()->notNull()->comment('id исполнителя'),
            'price' => $this->integer()->notNull(),
            'comment' => $this->text()->null(),
            'add_date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'rating' => $this->integer()->defaultValue(0),
            'status' => $this->string(16)->null()->comment('accepted или rejected'),
        ]);

        $this->createIndex(
            'reply_ind',
            'replies',
            'add_date'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%replies}}');
    }
}
