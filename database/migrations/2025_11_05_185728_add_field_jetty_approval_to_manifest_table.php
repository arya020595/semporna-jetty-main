<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJettyApprovalToManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest', function (Blueprint $table) {
            $table->after("jetty_approval_status", function (Blueprint $table) {
                $table->text("jetty_approval_comments")->nullable();
                $table->foreignId("jetty_approval_user_id")->nullable()->index("jetty_approval_user_id_index");
            });
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
            $table->dropIndex("jetty_approval_user_id_index");
            $table->dropColumn("jetty_approval_user_id");
            $table->dropColumn("jetty_approval_comments");
        });
    }
}
