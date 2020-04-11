<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Governorates::class, function (Faker\Generator $faker) {

    /*return [
        'name_en' => $faker->name,
        'name_ar' =>  $faker->name,
        'status' => 1,
		'created_at' => $faker->created_at,
		'updated_at' => $faker->created_at,
		
    ];*/
});
