<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otpable', function (Blueprint $table) {
            $table->id();
            $table->uuid("code")->index("code_index");

            $table->string("otpable_type")->nullable();
            $table->foreignId("otpable_id")->nullable();

            $table->string("otp");
            $table->string("address");
            $table->tinyInteger("type");

            $table->timestamp("expired_at");

            $table->timestamps();
            $table->timestamp("deleted_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otpable');
    }
}
