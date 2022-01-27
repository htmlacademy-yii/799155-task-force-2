<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%documents}}`.
 */
class m211106_161535_create_documents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%documents}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'link' => $this->string(512)->notNull(),
            'size' => $this->integer()->defaultValue(0),
        ]);
        $this->createIndex(
            'doc_ind',
            'documents',
            'task_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%documents}}');
    }
}
