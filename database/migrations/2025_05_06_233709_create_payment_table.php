<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("reciept_number");
            $table->string("first_name");
            $table->string("last_name")->nullable();
            $table->string("credit_card_no")->nullable();
            $table->string("security_code")->nullable();
            $table->string("card_expiration")->nullable();

            $table->decimal("amount")->nullable();

            $table->string("payment_method");
            $table->string("status");

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
        Schema::dropIfExists('payment');
    }
}
