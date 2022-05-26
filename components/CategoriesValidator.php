<?php

namespace app\components;

use Yii;
use yii\validators\Validator;
use app\models\Categories;
use app\models\ProfileData;

/**
 * Класс служит для валидации категорий
 */

class CategoriesValidator extends Validator
{
    /**
     * Проверка категорий на стороне сервера
     * Выводит в форму сообщение об ошибке
     * @param ProfileData $model объект класса
     * @param string $attribute имя проверяемого атрибута
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model->categoriesCheckArray === Categories::CATEGORIES_NOT_SELECTED) {
            $this->addError($model, $attribute, 'Не выбраны категории специализаций!');
        }
    }
}
