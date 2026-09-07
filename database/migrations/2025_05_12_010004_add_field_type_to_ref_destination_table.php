<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTypeToRefDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ref_destination', function (Blueprint $table) {
            $table->tinyInteger("type")->default(1)->index("type_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ref_destination', function (Blueprint $table) {
            $table->dropIndex("type_index");
            $table->dropColumn("type");
        });
    }
}
