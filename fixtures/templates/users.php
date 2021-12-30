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
    'contractor' => $faker->numberBetween(0, 1),
    'city' => $faker->numberBetween(1, 20),
];
