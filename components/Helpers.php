<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\User;

class Helpers extends Component
{
    /**
     * Возвращает корректную форму множественного числа
     * Ограничения: только для целых чисел
     *
     * @param int $number Число, по которому вычисляем форму множественного числа
     * @param string $one Форма единственного числа: яблоко, час, минута
     * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
     * @param string $many Форма множественного числа для остальных чисел
     *
     * @return string Рассчитанная форма множественнго числа
     */
    public function getNounPluralForm(int $number, string $one, string $two, string $many): string
    {
        $number = (int) $number;
        $mod10 = $number % 10;
        $mod100 = $number % 100;
        switch (true) {
            case ($mod100 >= 11 && $mod100 <= 20):
                return $many;
            case ($mod10 > 5):
                return $many;
            case ($mod10 === 1):
                return $one;
            case ($mod10 >= 2 && $mod10 <= 4):
                return $two;
            default:
                return $many;
        }
    }

    /**
     * Возвращает строку-сообщение о том, сколько времени прошло с какого-то события
     *
     * @param string $dt_add время наступления события
     *
     * @return string строка, содержащая сообщение о том, сколько времени прошло в свободном формате
    */
    public function getTimeStr($dt_add): string
    {
        $add = strtotime($dt_add);
        $nowArray = getdate();
        $addArray = getdate($add);

        $years = $nowArray['year'] - $addArray['year'];
        $months = $nowArray['mon'] - $addArray['mon'];
        $days = $nowArray['mday'] - $addArray['mday'];
        $hours = $nowArray['hours'] - $addArray['hours'];
        $mins = $nowArray['minutes'] - $addArray['minutes'];

        if ($years > 0) {
            return $years . " " . $this->getNounPluralForm($years, "год", "года", "лет") . " назад";
        }
        if ($months > 0 || $days > 1) {
            return date("d.m.Y в H:i", $add);
        } elseif ($days === 1) {
            return date("вчера, в H:i", $add);
        } elseif ($hours > 1) {
            return date("в H:i", $add);
        } elseif ($hours === 1) {
            return date("час назад");
        }
        if ($mins === 0) {
            return "меньше минуты назад";
        }
        return $mins . " " . $this->getNounPluralForm($mins, "минута", "минуты", "минут") . " назад";
    }

    /**
     * Возвращает строку-дата в виде 01 января 2021, 14:00
     *
     * @param string $strDate дата в стоковом формате
     * @param bool $hours true, если нужны еще и часы:минуты
     *
     * @return string дата с названием месяца на русском языке
    */
    public function ruDate(string $strDate, bool $hours = false): string
    {
        $date = new \DateTime($strDate);
        // номер месяца
        $index = $date->format('n') - 1;
        $months = [
            'Января', 'Февраля', 'Марта', 'Апреля', 'Майя', 'Июня', 'Июля',
            'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
            ];
        $format = "d $months[$index] Y" . ($hours ? ", H:i" : "");
        return $date->format($format);
    }

    /**
     * Возвращает разницу в годах
     * @param string $born дата в стоковом формате
     *
     * @return int|null разница в годах
    */
    public function getAge(?string $born): ?int
    {
        if ($born === null) {
            return null;
        }
        $date = getdate(strtotime($born));
        $now = getdate();
        return $now['year'] - $date['year'];
    }

    /**
     * Возвращает строку с телефонным номером в соответствии с маской
     * @param string $mask маска номера, в кторой символ # будет заменён на
     * очередной символ из $number
     * @param string $number строка с цифрами телефонного номера
     *
     * @return string строка с телефонным номером
     */
    public function translatePhoneNumber(string $mask, string $number): string
    {
        $phone = [];
        $digits = str_split($number);
        $symbols = str_split($mask);
        foreach ($symbols as $char) {
            if ($char !== '#') {
                $phone[] = $char;
            } else {
                $phone[] = current($digits);
                next($digits);
            }
        }
        return implode($phone);
    }

    /**
     * Возращает первую строку с описанием ошибки
     * @param ActiveRecord $model
     *
     * @return string|null первая же строка с описанием ошибки из массива $errors
     */
    public function getFirstErrorString($model): ?string
    {
        $errors = $model->getErrors();
        $names = array_keys($model->attributes);
        foreach ($names as $name) {
            if (array_key_exists($name, $errors)) {
                return $errors[$name][0];
            }
        }
        return null;
    }

    /**
     * Проверка авторизации
     * @return object|null возвращает сущность пользователя, если
     * он авторизован, или null в противном случае
     */
    public function checkAuthorization(): ?object
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return User::findIdentity(Yii::$app->user->getId());
    }

    /**
     * Проверка валидности загруженного на сервер файла
     * @param UploadedFile $file данные о загруженном файле
     * @return bool результат валидации
     */
    public function validateUploadedFile(UploadedFile $file, array $mimeTypes): bool
    {
        $errMsg = 'Ошибка загрузки файла: ';
        switch ($file->error) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException($errMsg . 'файл не был отправлен.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException($errMsg . 'превышен размер файла.');
            default:
                throw new \RuntimeException($errMsg . 'другие ошибки.');
        }
        if ($file->size > 1000000) {
            throw new \RuntimeException($errMsg . 'превышен размер файла.');
        }
        $mimeType = FileHelper::getMimeType($file->tempName);
        if (in_array($mimeType, $mimeTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Укорачивает имя файла до 30 символов, сохраняя расширение
     * @param string $fname имя файла
     * @return string укороченное имя файла
     */
    public function shortenFileName(string $fname): string
    {
        if (strlen($fname) < 31) {
            return $fname;
        }
        $ext = substr($fname, strpos($fname, '.'), 5);
        $shortName = substr($fname, 0, 20) . '... ' . $ext;
        return $shortName;
    }

    /**
     * Получает информацию о ресурсе из Вконтакте
     * Пример использования для получения списка фотографий
     *   $method = 'photos.get';
     *   $params = [
     *       'owner_id' => $attributes['user_id'],
     *       'album_id' => 'profile',
     *       'access_token' => $client->getAccessToken()->getParams()['access_token'],
     *       'v' => '5.131',
     *   ];
     *   $res = Yii::$app->helpers->api($method, $params);
     */
    public function api(string $method, $params = array())
    {
        $url = 'https://api.vk.com/method/' . $method . '?' . http_build_query($params);
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}
