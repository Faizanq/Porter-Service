<?php

use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
    
    $cms_data [] = ['code'=>'terms','title'=>'Terms & Conditions','filename'=>'terms.txt'];

    $cms_data [] = ['code'=>'about','title'=>'About Us','filename'=>'about.txt'];

    $cms_data [] = ['code'=>'privacy','title'=>'Privacy','filename'=>'privacy.txt'];

    foreach ($cms_data as $key => $value) {

      if(file_exists(storage_path("seedingFiles/".$value['filename']))){
	      
	      $filename = storage_path("seedingFiles/".$value['filename']);
          $content = File::get($filename);

           DB::table('cms')->insert([
            'code' => $value['code'],
            'title' => $value['title'],
            'description' => $content,
	       ]);

      }
    }//forach loop ends here

        
    }
}
