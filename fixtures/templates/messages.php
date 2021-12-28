<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'content' => $faker->sentence(7, true),
    'from_id' => $index + 1,
    'whom_id' => $faker->randomDigitNot($index + 1),
    'add_date' => $faker->date() . ' ' . $faker->time('H:i:s'),
];
