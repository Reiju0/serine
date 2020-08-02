<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->increments('id');
                $table->string('menu');
                $table->integer('is_parent')->default('0');
                $table->integer('parent_id')->default('0');
                $table->string('permission', '4000')->nullable();
                $table->integer('status')->default('1');
                $table->integer('have_link')->default('1');
                $table->string('path')->nullable();
                $table->string('style')->nullable();
                $table->integer('delta')->default('1');
                
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permission')) {
            Schema::create('permission', function (Blueprint $table) {
                $table->increments('id');
                $table->string('module');
                $table->string('permission')->nullable();

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
        Schema::dropIfExists('menu');
        Schema::dropIfExists('permission');
    }
}
