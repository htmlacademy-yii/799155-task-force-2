<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->firstName(),
    'email' => $faker->email(),
    'password' => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'add_date' => $faker->date(),
];
