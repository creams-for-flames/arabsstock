<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Models\SessionLocation::class, function (Faker $faker) {
    return [
        'name' => $this->faker->name,
        'admin' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'mobile' => $this->faker->phoneNumber,
        'license_code' => $this->faker->unique()->word,
        'location' => $this->faker->city,
    ];
});
