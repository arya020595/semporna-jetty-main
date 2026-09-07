<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJettyApprovalStatusToManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->integer("jetty_approval_status")->after("status");
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
            $table->dropColumn("jetty_approval_status");
        });
    }
}
