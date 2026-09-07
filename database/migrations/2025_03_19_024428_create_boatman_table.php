<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoatmanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boatman', function (Blueprint $table) {
            $table->id();
            $table->foreignId("boat_id")->nullable();
            $table->foreignId("company_id");
            $table->string("name");
            $table->string("ic_no")->nullable();
            $table->string("mate_card")->nullable();
            $table->string("seaman_card_no")->nullable();
            $table->integer("type");

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
        Schema::dropIfExists('boatman');
    }
}
