<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_group')) {
            Schema::create('user_group', function (Blueprint $table) {
                $table->increments('id');
                $table->string('slug')->nullable();
                $table->string('name')->nullable();
                $table->string('ref_table')->nullable();
                $table->string('kolom')->nullable();
                $table->string('nama')->nullable();
                $table->string('parent')->nullable();
                
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
        Schema::dropIfExists('user_group');
    }
}
