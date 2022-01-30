<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'task_id' => $index + 1,
    'link' => $faker->file('web/img', 'data/uploads', false),
    'size' => $faker->numberBetween(512, 4096),
];
