<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'user_id' => $index + 1,
    'born_date' => $faker->date(),
    'avatar' => $faker->image(null, 120, 120, 'faces', true),
    'last_act' => $faker->date() . ' ' . $faker->time('H:i:s'),
    'phone' => substr($faker->e164PhoneNumber, 1, 11),
    'messenger' => null,
    'social_net' => null,
    'address' => $faker->address(),
    'about_info' => $faker->sentence(7, true), // generate a sentence with 7 words
    'city' => $faker->city(),
    'categories' => null,
];
