<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ToDoList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('ref_jenis')) {
            Schema::create('ref_jenis', function (Blueprint $table) {
                $table->string('kdjenis')->unique();
                $table->string('jenis')->default(0);
                $table->string('status')->default(1);
                $table->integer('uid')->nullable();
                // $table->foreign('uid')->references('id')->on('users');
                $table->timestamps();
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
        Schema::dropIfExists('ref_satker');
    }
}
