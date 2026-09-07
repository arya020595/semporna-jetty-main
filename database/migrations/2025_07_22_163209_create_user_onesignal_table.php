<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOnesignalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_onesignal', function (Blueprint $table) {
            $table->id();

            $table->foreignId("user_id")->index("user_id_index");
            $table->string("uid");
            $table->string("device")->nullable();
            $table->string("type")->nullable();
            $table->tinyInteger("is_active")->nullable();

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
        Schema::dropIfExists('user_onesignal');
    }
}
