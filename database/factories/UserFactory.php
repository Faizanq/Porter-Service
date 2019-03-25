<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Cms;
use App\Models\Company;
use App\Models\Language;
use App\Models\Package;
use App\Models\Position;
use App\Models\Reason;
use App\Models\Report;
use App\Models\UserPackage;
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


$factory->define(Admin::class, function (Faker $faker) {
    return [
        'name' => 'Admin',
        'email' => 'admin@porter.com',
        'password'=>bcrypt('123123'),
    ];
});



$factory->define(Package::class, function (Faker $faker) {
    return [
        'name' => 'New Register',
        'description' => '2 free keys on successful registration',
        'key'=> 2,
        'price'=>0,
        'sku'=>'test'
    ];
});


$factory->define(Language::class, function (Faker $faker) {
    return [
        'language_code' => str_random(2),
        'language_name' => $faker->word,
    ];
});

$factory->define(Reason::class, function (Faker $faker) {
    return [
        'reason' => $faker->paragraph(1),
    ];
});

$factory->define(Report::class, function (Faker $faker) {
    return [
        'reason' => $faker->paragraph(1),
        'image' =>'http://lorempixel.com/100/100/',
    ];
});

$factory->define(Position::class, function (Faker $faker) {
    return [
        'position' => $faker->word,
    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'image' =>'http://lorempixel.com/100/100/',
    ];
});

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'logo' =>'http://lorempixel.com/100/100/',
    ];
});

// $factory->define(Cms::class, function (Faker $faker) {
    
//     dd('in Cms',$faker);
//     // if(file_exists(storage_path("seedingFiles/cities.txt"))){
//     //     $filename = storage_path("seedingFiles/cities.txt");
//     //     // $content = File::get($filename);
        
//     //     foreach(file($filename) as $line) { 
//     //         echo $line;
//     //     }
//     // }
//     return [
//         'code' => '',
//         'title' =>'',
//         'description' =>'',
//     ];
// });

