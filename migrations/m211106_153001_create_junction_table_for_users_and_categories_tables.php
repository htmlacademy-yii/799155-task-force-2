<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users_categories}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%users}}`
 * - `{{%categories}}`
 */
class m211106_153001_create_junction_table_for_users_and_categories_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users_categories}}', [
            'user_id' => $this->integer(),
            'category_id' => $this->integer(),
            'PRIMARY KEY(user_id, category_id)',
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-users_categories-user_id}}',
            '{{%users_categories}}',
            'user_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-users_categories-user_id}}',
            '{{%users_categories}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            '{{%idx-users_categories-category_id}}',
            '{{%users_categories}}',
            'category_id'
        );

        // add foreign key for table `{{%categories}}`
        $this->addForeignKey(
            '{{%fk-users_categories-category_id}}',
            '{{%users_categories}}',
            'category_id',
            '{{%categories}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-users_categories-user_id}}',
            '{{%users_categories}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-users_categories-user_id}}',
            '{{%users_categories}}'
        );

        // drops foreign key for table `{{%categories}}`
        $this->dropForeignKey(
            '{{%fk-users_categories-category_id}}',
            '{{%users_categories}}'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            '{{%idx-users_categories-category_id}}',
            '{{%users_categories}}'
        );

        $this->dropTable('{{%users_categories}}');
    }
}
