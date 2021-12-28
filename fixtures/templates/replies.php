<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'task_id' => $index + 1,
    'contr_id' => $faker->numberBetween(1, 10),
    'price' => $faker->randomNumber(5, false),
    'comment' => $faker->realText(),
    'add_date' => $faker->date(),
    'rating' => $faker->numberBetween(3, 10),
    'status' => $faker->randomElement(['accepted', 'rejected']),
];
