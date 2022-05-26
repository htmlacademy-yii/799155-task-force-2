<?php

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

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;

class Location extends ActiveRecord
{
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
     * Возвращает результат запроса к API Yandex
     * @param string $city наименование локации
     * @return array данные локации
     */
    public static function getApiRequest($city): array
    {
        $apiKey = Yii::$app->params['mapApiKey'];
        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/1.x/',
        ]);
        $responseData = [];
        try {
            $request = new Request('GET', '');
            $response = $client->send($request, [
                'query' => [
                    'kind' => 'locality',
                    'format' => 'json',
                    'apikey' => $apiKey,
                    'geocode' => $name,
                ],
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new BadResponseException("Response error: " . $response->getReasonPhrase(), $request, $response);
            }
            $content = $response->getBody()->getContents();
            $responseData = json_decode($content, true);
            if (json_last_error() !== \JSON_ERROR_NONE) {
                throw new ServerException("Invalid json format", $request, $response);
            }
            if ($error = ArrayHelper::getValue($responseData, 'error.info')) {
                throw new BadResponseException("API error: " . $error, $request, $response);
            }
        } catch (RequestException $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            throw new BadResponseException("Request error: " . $e->getMessage());
        }
        return $responseData;
    }
    
    /**
     * Возвращает геоданные: долготу и широту локации
     * @param string $name наименование локации
     *
     * @return array данные локации
     */
    public static function getGeoData(string $name): array
    {
        $city = City::findOne(['name' => $name]);
        if ($city) {
            return [
                'id' => $city->id,
                'lat' => $city->latitude,
                'lon' => $city->longitude,
            ];
        }
        $data = [
            'id' => 0,
            'lat' => 0,
            'lon' => 0,
        ];
        try {
            $responseData = self::getApiRequest($name);
            $featureMember = $responseData['response']['GeoObjectCollection']['featureMember'];
            $pos = $featureMember[0]['GeoObject']['Point']['pos'];
            $blank = strpos($pos, ' ');
            $city = new City();
            $city->name = $city;
            $city->longitude = substr($pos, 0, $blank);
            $city->latitude = substr($pos, $blank + 1);
            if ($city->save() === true) {
                $data['id'] = $city->id;
                $data['lon'] = $city->longitude;
                $data['lat'] = $city->latitude;
            }
        } catch (Exception $e) {
        }
        return $data;
    }

    /**
     * Сохраняет данные локации задания
     * @param TasksSelector $model задание
     *
     * @return bool результат сохранения данных в базе
    */
    public static function saveLocation(TasksSelector $model): bool
    {
        if (empty($model->city)) {
            //задание без привязки не криминал
            return true;
        }
        $city = City::findOne(['name' => $model->city]);
        if (!$city) {
            $props = [
                'name' => $model->city,
                'longitude' => $model->longitude,
                'latitude' => $model->latitude,
            ];
            $city = new City();
            $city->attributes = $props;
            if ($city->save() === false) {
                $message = 'Не удалось сохранить город. Ошибка: ';
                $message .= Yii::$app->helpers->getFirstErrorString($city);
                Yii::$app->getSession()->setFlash('error', $message);
            }
        }
        $loc = new Location();
        $props = [
            'city_id' => $city->id,
            'latitude' => $model->latitude,
            'longitude' => $model->longitude,
            'district' => $model->district,
            'street' => $model->street,
            'task_id' => $model->id,
        ];
        $loc->attributes = $props;
        return $loc->save();
    }

    /**
     * Возвращает координаты локации
     * @param int $taskId id задания
     *
     * @return array долгота и широта локации
     */
    public static function getGeoLocation(int $taskId): ?array
    {
        $loc = Location::findOne(['task_id' => $taskId]);
        if ($loc) {
            return [
                'id' => $loc->city_id,
                'lon' => $loc->longitude,
                'lat' => $loc->latitude,
            ];
        }
        return null;
    }
}
