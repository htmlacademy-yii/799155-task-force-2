<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $add_date
 * @property int $city_id
 * @property int $contractor
 *
 * @property Category[] $categories
 * @property UsersCategories[] $usersCategories
 */
class User extends ActiveRecord implements IdentityInterface
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
            [['name', 'email', 'password', 'contractor'], 'required'],
            [['name', 'email', 'password', 'add_date', 'city_id', 'contractor'], 'safe'],
            [['name', 'email', 'password'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['city_id', 'contractor'], 'integer'],
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

    /**
     * реализация методов интерфейса IdentityInterface
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
