<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use TaskForce\exception\TaskForceException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $icon
 *
 * @property User[] $users
 * @property UsersCategories[] $usersCategories
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 64],
            [['icon'], 'string', 'max' => 256],
            [['code'], 'unique'],
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
            'code' => 'Code',
            'icon' => 'Icon',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(
            User::className(),
            ['id' => 'user_id']
        )->viaTable('users_categories', ['category_id' => 'id']);
    }

    /**
     * Gets query for [[UsersCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersCategories()
    {
        return $this->hasMany(UsersCategories::className(), ['category_id' => 'id']);
    }

    /**
     * Возвращает массив категорий заданий из БД
     *
     * @return array [id => name]
     */
    public static function getCategoryNames(): array
    {
        $cats = self::find()->select("*")->all();
        return ArrayHelper::map($cats, 'id', 'name');
    }

    /**
     * Возвращает название категории из БД
     * @param int $id индекс категории
     *
     * @return string $name имя категории
     */
    public static function getName(int $id): string
    {
        $cat = self::findOne(['id' => $id]);
        if (!$cat) {
            throw new TaskForceException('Категория с id= ' . $id . ' нет в БД');
        }
        return $cat->name;
    }

    public static function getId(string $name): int
    {
        $cat = self::findOne(['name' => $name]);
        if (!$cat) {
            throw new TaskForceException('Категории ' . $name . ' нет в БД');
        }
        return $cat->id;
    }
}
