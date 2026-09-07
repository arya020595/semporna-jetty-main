<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldSequenceToManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->integer("sequence")->nullable()->change()->index("sequence_index");
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
            $table->string("sequence")->change();
        });
    }
}
