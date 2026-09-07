<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldActivityIdToGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId("activity_id")->nullable()->change();
            $table->string("activity_name")->nullable()->change();
            $table->foreignId("nationality_id")->nullable()->change();
            $table->string("nationality_name")->nullable()->change();
            $table->string("next_of_kin")->nullable()->change();
            $table->string("emergency_contact")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId("activity_id")->nullable(false)->change();
            $table->string("activity_name")->nullable(false)->change();
            $table->foreignId("nationality_id")->nullable(false)->change();
            $table->string("nationality_name")->nullable(false)->change();
            $table->string("next_of_kin")->nullable(false)->change();
            $table->string("emergency_contact")->nullable(false)->change();
        });
    }
}
