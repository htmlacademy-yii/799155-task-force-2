<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$codes = ['translation','clean','cargo','neo','flat','repair','beauty','photo'];
$names = ['Курьерские услуги', 'Уборка', 'Переезды', 'Ремонт квартирный', 'Ремонт техники', 'Красота','Фото'];
return $index < 7 ? [
    'name' => $names[$index],
    'code' => $codes[$index],
    'icon' => null,
] : [
    'name' => $names[6] . '_' . $index,
    'code' => $codes[6],
    'icon' => null,
];
