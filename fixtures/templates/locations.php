<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'city_id' => $index + 1,
    'latitude' => $faker->randomFloat(8, 0, 90),
    'longitude' => $faker->randomFloat(8, -180, 180),
    'district' => null,
    'street' => $faker->streetName(),
    'info' => $faker->sentence(5, true),
    'task_id' => $faker->numberBetween(1, 10),
];
