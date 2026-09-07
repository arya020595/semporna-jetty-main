<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index('code_index');
            $table->string('staf_id')->nullable()->index('staf_id_index');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('tel_no')->nullable();
            $table->string('ic_no', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId("created_by")->nullable();
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
        Schema::dropIfExists('users');
    }
}
