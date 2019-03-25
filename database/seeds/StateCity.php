<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateCity extends Seeder
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

            // $stateObject =  DB::table('states')->insert([
            // 'name' => $state,
            //  ]);
          		$stateObject = new State();
          		$stateObject->name = $state;
          		$stateObject->save();

             foreach ($cities as $key => $city) {

             	$cityObject = new City();
          		$cityObject->name = $city;
          		$cityObject->state_id = $stateObject->id;
          		$cityObject->save();

              //  DB::table('cities')->insert([
              // 'name' => $city,
              // 'state_id'=>$stateObject->id,
              //  ]);
            }
          }
      }
    }
}
