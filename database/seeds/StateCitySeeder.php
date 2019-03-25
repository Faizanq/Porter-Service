<?php

use Illuminate\Database\Seeder;

class StateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'stateCities.php';
      if(file_exists(storage_path("seedingFiles/".$File))){
        
        $arrays = include storage_path("seedingFiles/".$File);

          foreach ($arrays as $state => $cities) {

            $stateObject =  DB::table('states')->insert([
            'name' => $state,
             ]);

             foreach ($cities as $key => $city) {

               DB::table('cities')->insert([
              'name' => $city,
              'state_id'=>$stateObject->id,
               ]);
            }
          }
      }
    }
}
