<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('driver_id')->nullable();

            $table->string('bagage')->nullable();
            $table->string('pending_bagage')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('isInvalid')->default('N');
            $table->string('is_storage')->default('N');


            $table->string('pickup_address')->nullable();
            $table->string('pickup_latitude')->nullable();
            $table->string('pickup_longitude')->nullable();
            $table->string('sender_data')->nullable();

            $table->string('distance')->nullable();


            $table->string('dropoff_address')->nullable();
            $table->string('dropoff_latitude')->nullable();
            $table->string('dropoff_longitude')->nullable();
            $table->string('receiver_data')->nullable();


            $table->string('status')->nullable();
            $table->double('price')->nullable();

            $table->string('cancelation_message')->nullable();
            $table->string('is_payment_received')->nullable();
            $table->string('payment_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
