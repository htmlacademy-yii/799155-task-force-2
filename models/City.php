<?php

namespace app\models;

use TaskForce\exception\TaskForceException;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 32],
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
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Возвращает массив городов из БД
     *
     * @return array [id => name]
     */
    public static function getCityNames(): array
    {
        $cities = self::find()->select("*")->all();
        return ArrayHelper::map($cities, 'id', 'name');
    }

    /**
     * Возвращает индекс города из БД
     * @param string $name название города
     *
     * @return int $id
     */
    public static function getId(string $name): int
    {
        $city = self::find()->select([
            'id'
        ])->where(['name' => $name])->one();
        if (!$city) {
            throw new TaskForceException('Города с именем ' . $name . ' нет в БД');
        }
        return $city->id;
    }

    /**
     * Возвращает имя города из БД
     * @param int $id индекс города из БД
     *
     * @return string $name
     */
    public static function getName($id): string
    {
        $city = self::find()->select([
            'name'
        ])->where(['id' => $id])->one();
        if (!$city) {
            throw new TaskForceException('Города с id= ' . $id . ' нет в БД');
        }
        return $city->name;
    }
}
