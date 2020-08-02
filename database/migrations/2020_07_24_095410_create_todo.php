<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo', function (Blueprint $table) {
            $table->increments('id');
			$table->string('kdsatker',6);
			$table->string('kdjenis',2);
			$table->string('priority',2);
			$table->string('title',255);
			$table->string('description',500);
			$table->date('due_date');
			$table->date('done_date');
			$table->integer('status')->default(0);
			$table->integer('is_mass')->default(0);
			$table->string('mass_id',255);
			$table->integer('created_by');
			$table->integer('updated_by');
			//$table->date('created_at');
			$table->integer('deleted_by');
			$table->dateTime('deleted_at',0);
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo');
    }
}
