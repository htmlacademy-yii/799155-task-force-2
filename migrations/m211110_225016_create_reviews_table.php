<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reviews}}`.
 */
class m211110_225016_create_reviews_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%reviews}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'custom_id' => $this->integer()->notNull()->comment('заказчик работы'),
            'contr_id' => $this->integer()->notNull()->comment('исполнитель работы'),
            'add_date' =>  => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'comment' => $this->text()->null()->comment('отзыв заказчика о работе'),
            'rating' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%reviews}}');
    }
}
