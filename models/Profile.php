<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profiles".
 *
 * @property int $id
 * @property int $user_id
 * @property string $born_date
 * @property string|null $avatar
 * @property string|null $last_act дата последней активности
 * @property string|null $phone
 * @property string|null $messenger
 * @property string|null $social_net
 * @property string|null $address
 * @property string|null $about_info дополнительная информация о себе
 * @property string|null $categories
 * @property string|null $city
 * @property int $customer_only контакты показывать только для заказчика
 */
class Profile extends ActiveRecord
{
    //аноним
    public const ROLE_ANONYMOUS = 'anonymous';
    //роль исполнителя
    public const ROLE_CONTRACTOR = 'contractor';
    //роль заказчика
    public const ROLE_CUSTOMER = 'customer';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['born_date', 'last_act', 'categories', 'city', 'customer_only'], 'safe'],
            [['about_info', 'avatar'], 'string'],
            [['address'], 'string', 'max' => 256],
            [['phone', 'messenger', 'social_net'], 'string', 'max' => 32],
            ['customer_only', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'born_date' => 'Born Date',
            'avatar' => 'Avatar',
            'last_act' => 'Last Act',
            'phone' => 'Phone',
            'messenger' => 'Messenger',
            'social_net' => 'Social Net',
            'address' => 'Address',
            'about_info' => 'About Info',
            'categories' => 'Категории',
            'city' => 'Город',
            'customer_only' => 'Информация только для заказчика',
        ];
    }
}
