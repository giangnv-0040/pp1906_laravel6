<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Category;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$adminId = User::where('role', config('user.role.admin'))->first()->id;

$factory->define(Category::class, function (Faker $faker) use ($adminId) {
    return [
        'user_id' => $adminId,
        'parent_id' => null,
        'name' => $faker->realText($faker->numberBetween(10,20)),
    ];
});
