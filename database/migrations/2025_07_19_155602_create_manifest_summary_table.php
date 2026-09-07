<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_summary', function (Blueprint $table) {
            $table->id();

            $table->foreignId("manifest_id")->index("manifest_id");

            $table->integer("gender_male")->default(0);
            $table->integer("gender_female")->default(0);

            $table->integer("local_adult")->default(0);
            $table->integer("local_child")->default(0);
            $table->integer("foreign_adult")->default(0);
            $table->integer("foreign_child")->default(0);

            $table->decimal("charge_local_adult")->default(0);
            $table->decimal("charge_local_child")->default(0);
            $table->decimal("charge_foreign_adult")->default(0);
            $table->decimal("charge_foreign_child")->default(0);

            $table->decimal("charge_total_passenger")->default(0);

            $table->decimal("charge_boat_fee")->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manifest_summary');
    }
}
