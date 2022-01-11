<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $add_date
 *
 * @property Category[] $categories
 * @property UsersCategories[] $usersCategories
 */
class User extends \yii\db\ActiveRecord
{
    public const STATUS_FREE = 'Открыт для новых заказов';
    public const STATUS_BUSY = 'Занят';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'add_date', 'contractor'], 'required'],
            [['name', 'email', 'password', 'add_date', 'city_id', 'contractor'], 'safe'],
            [['name', 'email', 'password'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['city_id', 'contractor'], 'integer'],
            [['add_date'], 'date'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'add_date' => 'Add Date',
            'city_id' => 'ID города',
            'contractor' => 'Исполнитель?',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(
            Category::className(),
            ['id' => 'category_id']
        )->viaTable('users_categories', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UsersCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersCategories()
    {
        return $this->hasMany(UsersCategories::className(), ['user_id' => 'id']);
    }
}
