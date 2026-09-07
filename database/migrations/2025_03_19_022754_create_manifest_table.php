<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest', function (Blueprint $table) {
            $table->id();

            $table->foreignId("user_id")->index("user_id_index");
            $table->string("form_number");
            $table->string("form_date")->index("form_date_index");
            $table->string("sequence");

            $table->date("departure_date");
            $table->time("departure_time");

            $table->foreignId("departure_id")->index("departure_id_index");
            $table->string("departure_name");

            $table->tinyInteger("type");

            $table->foreignId("company_id")->nullable()->index("cokmpany_id_index");
            $table->string("company_name");

            $table->foreignId("boat_id")->nullable()->index("boat_id_index");
            $table->string("boat_number");

            $table->foreignId("boatman_id")->nullable()->index("boatman_id_index");
            $table->string("boatman_name");
            $table->string("boatman_mate_no");
            $table->string("seaman_no");
            $table->string("boatman_ic_no");

            $table->foreignId("assistant_id")->nullable()->index("assistant_id_index");
            $table->string("assistant_name");
            $table->string("assistant_mate_no");
            $table->string("assistant_ic_no");

            $table->integer("is_final");
            $table->integer("status");
            $table->integer("payment_status");

            $table->timestamps();
            $table->softDeletes();

            $table->index("created_at", "created_at_index");

            $table->foreignId("created_by")->nullable()->index("created_by_index");
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
        Schema::dropIfExists('manifest');
    }
}
