<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("manifest_id");
            $table->string("name", 255);
            $table->string("ic_no", 255)->nullable();

            $table->foreignId("nationality_id");
            $table->string("nationality_name");
            $table->foreignId("activity_id");
            $table->string("activity_name");

            $table->integer("age");
            $table->string("gender", 1);
            $table->string("next_of_kin");
            $table->string("emergency_contact");

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
        Schema::dropIfExists('guests');
    }
}
