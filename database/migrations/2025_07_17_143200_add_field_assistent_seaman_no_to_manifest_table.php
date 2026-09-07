<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldAssistentSeamanNoToManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->string("assistant_seaman_no")->after("assistant_ic_no")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->dropColumn("assistant_seaman_no");
        });
    }
}
