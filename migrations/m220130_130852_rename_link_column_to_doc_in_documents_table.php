<?php

use yii\db\Migration;

/**
 * Class m220130_130852_rename_link_column_to_doc_in_documents_table
 */
class m220130_130852_rename_link_column_to_doc_in_documents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%documents}}', 'link', 'doc');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%documents}}', 'doc', 'link');
    }
}
