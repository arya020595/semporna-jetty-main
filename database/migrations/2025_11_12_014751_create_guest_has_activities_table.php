<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestHasActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_has_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests');
            $table->foreignId('ref_activity_id')->constrained('ref_activity');
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
        Schema::dropIfExists('guest_has_activities');
    }
}
