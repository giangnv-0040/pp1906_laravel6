<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Faker\Generator as Faker;

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
$categoryIds = Category::limit(10)->pluck('id')->toArray();

$factory->define(Product::class, function (Faker $faker) use ($adminId, $categoryIds) {
    return [
        'category_id' => Arr::random($categoryIds),
        'user_id' => $adminId,
        'name' => $faker->realText($faker->numberBetween(10,20)),
        'content' => $faker->realText($faker->numberBetween(10,20)),
        'quantity' => $faker->numberBetween(10,20),
        'price' => $faker->numberBetween(10,20),
        'image' => null,
    ];
});
