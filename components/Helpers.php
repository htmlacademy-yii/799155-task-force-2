<?php

namespace app\components;

use yii\base\Component;

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
}