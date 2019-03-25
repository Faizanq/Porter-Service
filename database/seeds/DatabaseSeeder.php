<?php

use App\Models\Admin;
use App\Models\City;
use App\Models\Cms;
use App\Models\Reason;
use App\Models\State;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Reason::truncate();
        // Cms::truncate();
        // City::truncate();
        // State::truncate();


        $this->call([
        CmsSeeder::class,
        // ReasonSeeder::class,

        // StateCity::class,

        ]);
        // $this->call(UsersTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        
        $cmsQuantity = 4;

        // factory(Admin::class,1)->create();

        // factory(Language::class,$languageQuantity)->create();
        // factory(Reason::class,$reasonQuantity)->create();
        
        // factory(Report::class,$reportQuantity)->create();
        
        // factory(Category::class,$categoryQuantity)->create();
        
        // factory(Position::class,$positionQuantity)->create();

        // factory(Cms::class,$cmsQuantity)->create();

    }
}
