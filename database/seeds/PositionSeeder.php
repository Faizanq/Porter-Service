<?php

use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'positions.txt';
      if(file_exists(storage_path("seedingFiles/".$File))){
	      
	      $filename = storage_path("seedingFiles/".$File);
          foreach (file($filename) as $value) {
          	 DB::table('positions')->insert([
            'position' => $value,
	       ]);
          }
      }
    }
}
