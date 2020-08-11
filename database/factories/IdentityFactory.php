<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Identity;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Identity::class, function (Faker $faker) {
    return [
        'provider' => $faker->name,
        'provider_id' => $faker->name,
        'user_id' => factory(User::class),
        'token' => Str::random(10),
    ];
});
