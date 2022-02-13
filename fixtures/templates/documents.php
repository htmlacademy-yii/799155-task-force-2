<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$ext = $faker->randomElement(['doc', 'txt', 'docx']);
return [
    'task_id' => $index + 1,
    'fname' => 'upload' . $faker->randomNumber() . '.' . $ext,
    'size' => $faker->numberBetween(512, 4096),
    'doc' => $faker->word() . '.' . $ext,
];
