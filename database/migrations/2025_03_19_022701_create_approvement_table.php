<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvement', function (Blueprint $table) {
            $table->id();
            $table->foreignId("manifest_id");
            $table->foreignId("user_id");
            $table->foreignId("role_id");

            $table->text("comments");
            $table->text("options")->nullable();
            $table->dateTime("date");
            $table->integer("version");

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
        Schema::dropIfExists('approvement');
    }
}
