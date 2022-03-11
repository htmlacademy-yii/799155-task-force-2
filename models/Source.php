<?php

/**
 * This is the model class for table "source".
 *
 * @property int $id
 * @property int $user_id
 * @property int $source_id
 * @property string $source
 * @property string $add_date
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Source extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source';
    }

        /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user ID',
            'source_id' => 'source ID',
            'source' => 'Источник авторизации',
            'add_date' => 'Дата создания записи',
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'source_id'], 'integer'],
            [['source'], 'string'],
            [['add_date'], 'string'],
        ];
    }

    public static function getSource($user, $client)
    {
        $props = [
            'user_id' => $user->id,
            'source' => $client->source,
            'source_id' => $client->sourceId,
            'add_date' => date("Y-m-d H:i:s"),
        ];
        $source = new Source();
        $source->attributes = $props;
        return $source;
    }
}
