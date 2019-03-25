<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('image_url')->nullable();
            $table->string('dob')->nullable();

            $table->enum('is_profile_completed',['Y','N'])->default('N');
            $table->enum('is_notification',['Y','N'])->default('Y');
            $table->enum('is_mobile_verify',['Y','N'])->default('N');
            $table->enum('is_email_verify',['Y','N'])->default('N');
            $table->enum('is_accepted_terms',['Y','N'])->default('N');
            $table->enum('gender',['M','F','T'])->default('M');
            $table->enum('language',['S','E'])->default('S');


            $table->string('social_id')->nullable();
            $table->string('social_type')->nullable();

            $table->string('otp')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile_number')->nullable();

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();

            $table->string('user_type')->default('1');
            $table->string('email_verification_token_timeout')->nullable();
            $table->string('verify_email_token')->nullable();

            $table->enum('status',['Y','N'])->default('Y');
            $table->enum('online',['Y','N'])->default('Y');


            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
