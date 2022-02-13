<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%documents}}`.
 */
class m220129_232631_add_fname_column_to_documents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'fname', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'fname');
    }
}
