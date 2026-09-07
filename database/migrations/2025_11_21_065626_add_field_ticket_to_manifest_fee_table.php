<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTicketToManifestFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifest_fee', function (Blueprint $table) {
            $table->integer("ticket_local_child")->default(0);
            $table->integer("ticket_local_adult")->default(0);
            $table->integer("ticket_foreign_child")->default(0);
            $table->integer("ticket_foreign_adult")->default(0);
            $table->integer("total_ticket")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manifest_fee', function (Blueprint $table) {
            $table->dropColumn("ticket_local_child");
            $table->dropColumn("ticket_local_adult");
            $table->dropColumn("ticket_foreign_child");
            $table->dropColumn("ticket_foreign_adult");
            $table->dropColumn("total_ticket");
        });
    }
}
