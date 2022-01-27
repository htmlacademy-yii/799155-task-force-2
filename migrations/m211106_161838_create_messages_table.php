<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%messages}}`.
 */
class m211106_161838_create_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey(),
            'content' => $this->string(256)->notNull(),
            'from_id' => $this->integer()->notNull()->comment('от кого сообщение'),
            'whom_id' => $this->integer()->notNull()->comment('кому сообщение'),
            'add_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'msg_ind',
            'messages',
            'add_date'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%messages}}');
    }
}
