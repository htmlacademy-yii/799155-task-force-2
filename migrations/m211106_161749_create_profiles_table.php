<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profiles}}`.
 */
class m211106_161749_create_profiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%profiles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'born_date' => $this->date()->null(),
            'avatar' => $this->string(256)->null(),
            'last_act' => $this->dateTime()->null()->comment('дата последней активности'),
            'phone' => $this->string(32)->null(),
            'messenger' => $this->string(32)->null(),
            'social_net' => $this->string(32)->null(),
            'address' => $this->string(256)->null(),
            'about_info' => $this->text()->null()->comment('дополнительная информация о себе'),
            'city' => $this->string(32)->null(),
        ]);
        
        $this->createIndex(
            'profile_ind',
            'profiles',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%profiles}}');
    }
}
