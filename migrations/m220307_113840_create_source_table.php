<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%source}}`.
 */
class m220307_113840_create_source_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%source}}', [
            'id' => $this->primaryKey(),
            'source' => $this->string(64)->notNull()->comment('источник авторизации'),
            'source_id' => $this->integer()->notNull()->unique()->comment('id из источника'),
            'user_id' => $this->integer()->defaultValue(0)->comment('id пользователя'),
            'add_date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('source_unique', '{{%source}}', ['user_id', 'source_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%source}}');
    }
}
