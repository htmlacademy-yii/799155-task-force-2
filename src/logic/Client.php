<?php

/**
 * Класс содежит данные клинта,
 * полученные от API ВКонтакте
 * и служит для регистрации/авторизации на сайте
 */

namespace TaskForce\logic;

use Yii;
use app\models\User;
use app\models\Profile;
use app\models\Source;
use app\models\Logon;
use app\models\Location;
use app\models\ProfileFile;

/**
 * Класс служит для регистрации пользователя
 * через аккаунт ВКонтакте
 */
class Client
{
    public const PASSWORD_LENGTH = 8;
    public $source;
    public $sourceId;
    public $email;
    public $city;
    public $name;
    public $photo;
    public $bdate;
    public $check;
    public $accessToken;

    public function __construct($client)
    {
        $attributes = $client->getUserAttributes();
        $this->source = $client->getId();
        $this->sourceId = $attributes['user_id'];
        $this->email = $attributes['email'];
        $this->city = $attributes['city']['title'];
        $this->name = $attributes['first_name'];
        $time = strtotime($attributes['bdate']);
        $this->bdate = date('Y-m-d', $time);
        $this->photo = $attributes['photo'];
        $this->check = 0;
        $this->accessToken = $client->getAccessToken()->getParams()['access_token'];
    }

    public function attributeLabels()
    {
        return [
            'source' => 'vkontakte',
            'sourceId' => 'ID VKontakte',
            'email' => 'email',
            'city' => 'Город',
            'name' => 'Имя пользователя',
            'photo' => 'avatar',
            'bdate' => 'день рождения',
            'check' => 'эагрузить фото',
        ];
    }

    public function rules()
    {
        return [
            [['source', 'sourceId', 'email', 'city'], 'safe'],
            [['name', 'photo', 'bdate', 'check'], 'safe'],
            [['check', 'sourceId'], 'integer'],
            [['source', 'email', 'city'], 'string'],
            ['bdate', 'date'],
        ];
    }

    /**
     * Обработка данных регистрации пользователя
     */
    private function updateSource()
    {
        $user = Yii::$app->helpers->checkAuthorization();
        $auth = Source::getSource($user, $this);
        if (!$auth->save()) {
            $message = 'Ошибка: ';
            $message .= Yii::$app->helpers->getFirstErrorString($auth);
            Yii::$app->getSession()->setFlash('error', $message);
        }
    }

    /**
     * Регистрация пользователя
     * @param User $user сущность
     * @return true|false результат регистрации
     */
    private function registerUser(&$user): bool
    {
        if (
            !empty($this->email) &&
            User::find()->where(['email' => $this->email])->exists()
        ) {
            $message = 'Пользователь с такой электронной почтой как в ' . $this->source;
            $message .= ' уже существует';
            Yii::$app->getSession()->setFlash('info', $message);
            return false;
        }
        $geoData = null;
        if (!empty($this->city)) {
            $geoData = Location::getGeoData($this->city);
        }
        $props = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Yii::$app->security->generateRandomString(self::PASSWORD_LENGTH),
            'contractor' => 0,
            'city_id' => $geoData['id'],
        ];
        $user->attributes = $props;
        if ($user->save()) {
            if (!$this->createProfile($user)) {
                //профиль не создан
                //удаляем данные о пользователе
                $user->delete();
                return false;
            }
        } else {
            $message = 'Не удалось зарегистрировать пользователя. Ошибка: ';
            $message .= Yii::$app->helpers->getFirstErrorString($user);
            Yii::$app->getSession()->setFlash('error', $message);
            return false;
        }
        return true;
    }

    /**
     * Создает профиль пользователя
     * @param User $user данные регистрации пользователя
     * @return true|false результат создания профиля
     */
    private function createProfile($user): bool
    {
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->last_act = date("Y-m-d H:i:s");
        if (!empty($this->city)) {
            $profile->city = $this->city;
        }
        if (!empty($this->bdate)) {
            $time = strtotime($this->bdate);
            $profile->born_date = date('Y-m-d', $time);
        }
        $profile->avatar = ProfileFile::AVATAR_ANONIM;
        /* Загрузка аватара будет выполнена в SiteController через Client::loadPhoto() */
        if (!$profile->save()) {
            $message = 'Не удалось сохранить профиль. Ошибка: ';
            $message .= Yii::$app->helpers->getFirstErrorString($profile);
            Yii::$app->getSession()->setFlash('error', $message);
            return false;
        }
        return true;
    }

    /**
     * Производит авторизацию пользователя
     * через аккаунт ВКонтакте
     */
    public function authorizeClient()
    {
        if (!Yii::$app->user->isGuest) {
            //пользователь уже зарегистрирован и авторизован
            $this->updateSource();
            return;
        }
        $auth = Source::find()->where(
            [
                'source' => $this->source,
                'source_id' => $this->sourceId,
            ]
        )->one();
        if ($auth) { //пользователь уже заходил через ВКонтакте, авторизация
            $user = User::findOne($auth->user_id);
            $model = new Logon();
            $model->logon($user, true);
            return;
        }
        //пользователь не зарегистрирован
        //регистрация пользователя
        $user = new User();
        if ($this->registerUser($user)) {
            $auth = Source::getSource($user, $this);
            if ($auth->save()) {
                $model = new Logon();
                $model->logon($user, true);
                Yii::$app->getSession()['registration'] = true;
                Yii::$app->getSession()['token'] = $this->accessToken;
            } else {
                $message = 'Не удалось сохранить данные регистрации. Ошибка: ';
                $message .= Yii::$app->helpers->getFirstErrorString($auth);
                Yii::$app->getSession()->setFlash('error', $message);
            }
        }
    }

    /**
     * Загружает аватар пользователя
     * который зарегистрировался через ВКонтакте
     */
    public static function loadPhoto(): bool
    {
        $user = Yii::$app->helpers->checkAuthorization();
        if ($user !== null) {
            $source = Source::findOne(['user_id' => $user->id]);
            $profile = Profile::findOne(['user_id' => $user->id]);
            if ($source !== null) {
                //параметры запроса к API VKontakte
                $method = 'photos.get';
                $params = [
                    'owner_id' => $source['source_id'],
                    'album_id' => 'profile',
                    'access_token' => Yii::$app->getSession()['token'],
                    'v' => '5.131',
                ];
                //запрос на получение аватара из профиля VKontakte
                $res = Yii::$app->helpers->api($method, $params);
                if ($res['response']['count'] > 0) {
                    $url = $res['response']['items'][0]['sizes'][0]['url'];
                    $fileData = explode('?', $url);
                    $fileInfo = explode('/', $fileData[0]);
                    //имя файла аватара с расширением
                    $fileName = $fileInfo[count($fileInfo) - 1];
                    $fileExt = strstr($fileName, '.');
                    //новое имя файла, с которым он будет скопирован в базу
                    $fileName = uniqid('up') . $fileExt;
                    //скопируем содержимое файла в базу
                    $content = file_get_contents($url);
                    if ($content !== false) {
                        $handle = fopen(
                            Yii::$app->basePath . '/web' . Yii::$app->params['uploadPath'] . $fileName,
                            'w'
                        );
                        if ($handle !== false) {
                            fwrite($handle, $content);
                            fclose($handle);
                        }
                        $profile->avatar = Yii::$app->params['uploadPath'] . $fileName;
                        return $profile->update() !== false;
                    }
                }
            }
        }
        return false;
    }
}
