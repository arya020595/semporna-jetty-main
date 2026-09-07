<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableAssistantFieldToManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->string("assistant_name", 255)->nullable(true)->change();
            $table->string("assistant_mate_no", 255)->nullable(true)->change();
            $table->string("assistant_ic_no", 255)->nullable(true)->change();
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
            $table->string("assistant_name", 255)->nullable(false)->change();
            $table->string("assistant_mate_no", 255)->nullable(false)->change();
            $table->string("assistant_ic_no", 255)->nullable(false)->change();
        });
    }
}
