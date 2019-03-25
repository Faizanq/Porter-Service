<?php

use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'reasons.txt';
      if(file_exists(storage_path("seedingFiles/".$File))){
	      
	      $filename = storage_path("seedingFiles/".$File);
          foreach (file($filename) as $value) {
          	 DB::table('reasons')->insert([
            'reason' => $value,
	       ]);
          }
      }
    }
}
