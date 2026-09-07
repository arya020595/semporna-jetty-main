<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccessLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_access_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->index("user_id_index");
            $table->string("action", 128);
            $table->string("url", 255);
            $table->string("ip_address", 128);
            $table->text("user_agent");
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
        Schema::dropIfExists('user_access_log');
    }
}
