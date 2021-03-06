<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'custom_id' => $index + 1,
    'contr_id' => $faker->randomDigitNot($index + 1),
    'name' => $faker->jobTitle(),
    'description' => $faker->sentence(7, true),
    'cat_id' => $faker->numberBetween(3, 10),
    'budget' => $faker->randomNumber(5, false),
    'add_date' => $faker->date(),
    'deadline' => $faker->date(),
    'fin_date' => $faker->date(),
    'status' => $faker->randomElement(['new', 'done', 'refused', 'canceled']),
];
