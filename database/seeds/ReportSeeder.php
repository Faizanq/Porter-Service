<?php

use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $File = 'reports.txt';
      if(file_exists(storage_path("seedingFiles/".$File))){
	      
	      $filename = storage_path("seedingFiles/".$File);
          foreach (file($filename) as $value) {
          	 DB::table('reports')->insert([
            'reason' => $value,
            'image'=>'',
	       ]);
          }
      }
    }
}
