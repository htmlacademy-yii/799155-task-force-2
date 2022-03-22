<?php

namespace app\models;

use Yii;

/**
 * Класс для работы с профилем пользовтеля
 */
class ProfileData extends Profile
{
    public $name;
    public $email;
    public $categoriesCheckArray = Categories::CATEGORIES_NOT_SELECTED;
    public $contractor;
    public $town;
    public $longitude;
    public $latitude;

    public function __construct($prof, $user)
    {
        $this->name = $user->name;
        $this->phone = $prof->phone;
        $this->address = $prof->address;
        $this->email = $user->email;
        $this->avatar = $prof->avatar;
        $this->born_date = $prof->born_date;
        $this->messenger = $prof->messenger;
        $this->about_info = $prof->about_info;
        $this->contractor = $user->contractor;
        $this->town = $prof->city;
        $this->customer_only = 1;
    }

    public function rules()
    {
        return [
            [['born_date', 'last_act', 'messenger', 'categoriesCheckArray', 'town'], 'safe'],
            [['about_info', 'avatar', 'messenger'], 'string'],
            [['address'], 'string', 'max' => 256],
            [['phone', 'messenger', 'social_net'], 'string', 'max' => 32],
            [['name', 'email', 'messenger'], 'required', 'message' => 'Поле не может быть пустым'],
            ['customer_only', 'integer'],
            ['customer_only', 'safe'],
        ];
    }

    /**
     * Запись данных профиля в базу
     * @param Profile $prof профиль пользователя
     * @param User $user зарегистрированный пользователь
     * @return bool true, если запись прошла успешно
     */
    public function updateProfile($prof, $user): bool
    {
        $prof->phone = $this->phone;
        $prof->address = $this->address;
        $prof->born_date = $this->born_date;
        $prof->messenger = $this->messenger;
        $prof->about_info = $this->about_info;
        $prof->city = $this->town;
        $prof->customer_only = $this->customer_only;
        $prof->last_act = date("Y-m-d H:i:s");
        if ($prof->update() === false) {
            $message = 'Не удалось сохранить профиль. Ошибка: ';
            $message .= Yii::$app->helpers->getFirstErrorString($prof);
            Yii::$app->getSession()->setFlash('error', $message);
        }
        $city = City::findOne(['name' => $this->town]);
        if (!$city) {
            $props = [
                'name' => $this->town,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
            ];
            $city = new City();
            $city->attributes = $props;
            if ($city->save() === false) {
                $message = 'Не удалось сохранить город. Ошибка: ';
                $message .= Yii::$app->helpers->getFirstErrorString($city);
                Yii::$app->getSession()->setFlash('error', $message);
            }
        }
        $user->city_id = $city->id;
        $user->email = $this->email;
        $user->name = $this->name;
        if ($user->update() === false) {
            $message = 'Не удалось сохранить пользователя. Ошибка: ';
            $message .= Yii::$app->helpers->getFirstErrorString($user);
            Yii::$app->getSession()->setFlash('error', $message);
        }
        return true;
    }

    public function getCategoriesCheckArray()
    {
        return $this->categoriesCheckArray;
    }

    public function setCategoriesCheckArray($categories)
    {
        $this->categoriesCheckArray = $categories;
    }

    public static function codeCategories(array $categoryIds): string
    {
        return array_reduce($categoryIds, function ($out, $item) {
            $out .= chr($item);
            return $out;
        }, '');
    }

    public static function decodeCategories(?string $categories): array
    {
        if (empty($categories)) {
            return [];
        }
        return array_map(function ($item) {
            return ord($item);
        }, str_split($categories));
    }
}
