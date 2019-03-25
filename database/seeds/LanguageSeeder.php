<?php

use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'languages.txt';
      if(file_exists(storage_path("seedingFiles/".$File))){
	      
	      $filename = storage_path("seedingFiles/".$File);
          foreach (file($filename) as $value) {
             $value = explode(',', $value);
             if(is_array($value)){
          	 DB::table('languages')->insert([
            'language_code' => $value[0],
            'language_name' => $value[1],
    	       ]);
            }
          }
      }
    }
}
