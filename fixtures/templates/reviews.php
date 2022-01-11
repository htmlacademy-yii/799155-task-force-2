<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'task_id' => $index + 1,
    'contr_id' => $faker->numberBetween(1, 5),
    'custom_id' => $faker->numberBetween(6, 10),
    'comment' => $faker->realText(),
    'add_date' => $faker->date(),
    'rating' => $faker->numberBetween(0, 5),
];
