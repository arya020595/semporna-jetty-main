<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestDestinationActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_destination_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId("manifest_destination_id")->index("manifest_destination_id_index");
            $table->foreignId("ref_activity_id")->nullable()->index("ref_activity_id_index");
            $table->string("activity_name")->nullable();

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
        Schema::dropIfExists('manifest_destination_activity');
    }
}
