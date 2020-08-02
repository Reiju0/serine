<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('logs')) {
            Schema::create('logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id');
                $table->integer('year')->index();
                $table->text('logs');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('user_login')) {
            Schema::create('user_login', function (Blueprint $table){
                $table->bigIncrements('id');
                $table->integer('user_id')->index();
                $table->string('token')->index();
                $table->string('status')->default(1)->nullable();
                $table->timestamp('timeout')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('logs');
        Schema::dropIfExists('user_login');
    }
}
