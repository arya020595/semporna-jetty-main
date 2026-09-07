<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("manifest_id");
            $table->string("code", 64);
            $table->string("reciept_number", 64);
            $table->decimal("amount", 9, 2);
            $table->integer("payement_method");
            $table->integer("status");
            $table->timestamp("purchased_at")->nullable();
            $table->text("remark")->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId("created_by")->nullable();
            $table->foreignId("updated_by")->nullable();
            $table->foreignId("deleted_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
