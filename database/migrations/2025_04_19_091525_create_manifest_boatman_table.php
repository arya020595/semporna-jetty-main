<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestBoatmanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_boatman', function (Blueprint $table) {
            $table->id();
            $table->foreignId("boatman_id")->nullable();
            $table->foreignId("manifest_id");
            $table->string("name");
            $table->string("ic_no");
            $table->integer("type");

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manifest_bootman');
    }
}
