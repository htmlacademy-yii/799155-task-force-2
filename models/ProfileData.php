<?php

namespace app\models;

use Yii;

class ProfileData extends Profile
{
    public $name;
    public $email;
    public $categoriesCheckArray = Categories::CATEGORIES_NOT_SELECTED;
    public $contractor;

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
    }

    public function rules()
    {
        return [
            [['born_date', 'last_act', 'messenger', 'categoriesCheckArray'], 'safe'],
            [['about_info', 'avatar', 'messenger'], 'string'],
            [['address'], 'string', 'max' => 256],
            [['phone', 'messenger', 'social_net'], 'string', 'max' => 32],
            [['name', 'email', 'messenger'], 'required', 'message' => 'Поле не может быть пустым'],
        ];
    }

    public function updateProfile($prof, $user): bool
    {
        $prof->phone = $this->phone;
        $prof->address = $this->address;
        $prof->born_date = $this->born_date;
        $prof->messenger = $this->messenger;
        $prof->about_info = $this->about_info;
        $prof->last_act = date("Y-m-d H:i:s");
        $prof->update();
        $user->email = $this->email;
        $user->name = $this->name;
        $user->update();
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
