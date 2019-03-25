<?php

use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'areas.txt';
      if(file_exists(storage_path("seedingFiles/".$File))){
        
        $filename = storage_path("seedingFiles/".$File);
          foreach (file($filename) as $value) {
             DB::table('areas')->insert([
            'code' => '',
            'name' => $value,
         ]);
          }
      }
    }
}
