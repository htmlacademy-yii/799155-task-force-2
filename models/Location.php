<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "locations".
 *
 * @property int $id
 * @property int $city_id
 * @property int $task_id
 * @property float|null $latitude широта места
 * @property float|null $longitude долгота места
 * @property string|null $district район
 * @property string|null $street улица
 * @property string|null $info дополн. информация
 */
class Location extends ActiveRecord
{
    public const TYPE_CITY = 'city';
    public const TYPE_DISTRICT = 'district';
    public const TYPE_STREET = 'street';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'task_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['info'], 'string'],
            [['district', 'street'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'task_id' => 'Task ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'district' => 'District',
            'street' => 'Street',
            'info' => 'Info',
        ];
    }

    /**
     * Возвращает геоданные: долготу и широту локации
     * Если ищем данные только для self::TYPE_CITY, то параметр name не важен.
     * Если ищем данные по названию района или улицы, то следует
     * задать имя города и соответствующее название
     * @param string $city имя города
     * @param string $type тип локации: self::TYPE_CITY или
     * self::TYPE_DISTRICT или self::TYPE_STREET
     * @param string $name null или имя локации в соответствии с типом
     *
     * @return array данные локации или null
     *
     * Пока нет реальной работы с геоданными, функция-заглушка выдает координаты из БД
     * независимо от задания района и/или улицы
     */
    public static function getGeoData(string $city, string $type = Location::TYPE_CITY, string $name = null): ?array
    {
        $city = City::findOne(['name' => $city]);
        if ($city) {
            return [
                'id' => $city->id,
                'lat' => $city->latitude,
                'lon' => $city->longitude,
            ];
        }
        return null;
    }

    /**
     * Сохраняет данные локации задания
     * @param ActiveRecord $model задание
     * @return bool результат сохранения данных в базе
    */
    public static function saveLocation(ActiveRecord $model): bool
    {
        if (empty($model->city_id)) {
            //задание без привязки не криминал
            return true;
        }
        $loc = new Location();
        $props = [
            'city_id' => $model->city_id,
            'latitude' => $model->latitude,
            'longitude' => $model->longitude,
            'district' => $model->district,
            'street' => $model->street,
            'task_id' => $model->id,
        ];
        $loc->attributes = $props;
        return $loc->save();
    }
}
