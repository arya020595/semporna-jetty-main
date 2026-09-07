<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->id();
            $table->string("code", 255)->index("code_index");
            $table->string("name")->nullable();
            $table->string("category_name")->nullable();
            $table->foreignId("departure_id")->nullable()->index("departure_id_index");
            $table->foreignId("manifest_id")->nullable()->index("manifest_id_index");
            $table->string("manifest_form_number", 255)->nullable();
            $table->foreignId("passenger_id")->nullable()->index("passenger_id_index");
            $table->integer("status")->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->timestamp("taken_at")->nullable();
            $table->timestamp("use_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket');
    }
}
