<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'user_id' => $index + 1,
    'img' => $faker->image(null, 640, 480, 'pictures', true),
];
