<?php

namespace app\components;

use Yii;
use yii\base\Component;
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
     *
     * @return string дата с названием месяца на русском языке
    */
    public function ruDate($strDate): string
    {
        $date = new \DateTime($strDate);
        // номер месяца
        $index = $date->format('n') - 1;
        $months = [
            'Января', 'Февраля', 'Марта', 'Апреля', 'Майя', 'Июня', 'Июля',
            'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
            ];
        return $date->format("d $months[$index] Y, H:i");
    }

    /**
     * Возвращает разницу в годах
     * @param string $date дата в стоковом формате
     *
     * @return int разница в годах
    */
    public function getAge($born): int
    {
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

    public function checkAuthorization(): ?object
    {
        if (is_object(Yii::$app->user)) {
            $user = User::findIdentity(Yii::$app->user->getId());
            if (is_object($user)) {
                return $user;
            }
        }
        return null;
    }
}
