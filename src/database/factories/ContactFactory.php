<?php

use Faker\Generator;
use VCComponent\Laravel\Contact\Entities\Contact;

$factory->define(Contact::class, function (Generator $faker) {
    $firstName = $faker->firstName;
    $lastName  = $faker->lastName;
    return [
        'email'      => $faker->safeEmail,
        'full_name'  => $firstName . ' ' . $lastName,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'address'    => $faker->address,
    ];
});
