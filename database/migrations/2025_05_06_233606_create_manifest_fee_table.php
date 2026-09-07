<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_fee', function (Blueprint $table) {
            $table->id();
            $table->foreignId("manifest_id")->index("manifest_id_index");
            $table->foreignId("payment_id")->nullable()->index("payment_id_index");
            $table->tinyInteger("type")->index("type_index");

            $table->integer("local_child");
            $table->integer("local_adult");
            $table->integer("foreign_child");
            $table->integer("foreign_adult");

            $table->decimal("local_child_fee");
            $table->decimal("local_adult_fee");
            $table->decimal("foreign_child_fee");
            $table->decimal("foreign_adult_fee");

            $table->decimal("boat_fee");

            $table->decimal("total");

            $table->tinyInteger("status")->index("status_index");

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
        Schema::dropIfExists('manifest_fee');
    }
}
