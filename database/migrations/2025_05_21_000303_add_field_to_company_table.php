<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->string("number")->nullable()->index("number_index");
            $table->string("year_month", 7)->nullable()->index("year_month_index");
            $table->integer("sequence")->nullable()->index("sequence_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropIndex("number_index");
            $table->dropIndex("year_month_index");
            $table->dropIndex("sequence_index");

            $table->dropColumn("number");
            $table->dropColumn("year_month");
            $table->dropColumn("sequence");
        });
    }
}
