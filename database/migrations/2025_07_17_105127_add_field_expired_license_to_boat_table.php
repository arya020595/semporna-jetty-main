<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldExpiredLicenseToBoatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boat', function (Blueprint $table) {
            $table->date("license_expiry_date")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boat', function (Blueprint $table) {
            $table->dropColumn("license_expiry_date");
        });
    }
}
