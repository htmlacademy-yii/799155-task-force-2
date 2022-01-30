<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'user_id' => $index + 1,
    'img' => $faker->file('web/img', 'data/uploads', false),
];
