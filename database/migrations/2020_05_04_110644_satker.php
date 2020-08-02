<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Satker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //


        if (!Schema::hasTable('ref_dept')) {
            Schema::create('ref_dept', function (Blueprint $table) {
                $table->string('kddept')->unique();
                $table->string('nmdept')->default(0);
                $table->string('status')->default(1);
                $table->integer('uid')->nullable();
                // $table->foreign('uid')->references('id')->on('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ref_es1')) {
            Schema::create('ref_es1', function (Blueprint $table) {
                $table->string('kdbaes1')->unique();
                $table->string('kddept');
                $table->foreign('kddept')->references('kddept')->on('ref_dept');
                $table->string('kdunit', 2)->default(0);
                $table->string('nmunit')->default(0);
                $table->string('status')->default(1);
                $table->integer('uid')->nullable();
                // $table->foreign('uid')->references('id')->on('users');
                $table->timestamps();
            });
        }



        if (!Schema::hasTable('ref_kppn')) {
            Schema::create('ref_kppn', function (Blueprint $table) {
                $table->string('kdkppn')->unique();
                $table->string('nmkppn')->default(0);
                $table->string('kdkanwil');
                $table->string('kdlokasi')->nullable();
                $table->string('tipekppn')->nullable();
                $table->string('kdkppn1')->nullable();
                $table->string('kdsatkerbun')->nullable();
                $table->string('kdsatkerkppn')->nullable();
                $table->text('alamat')->nullable();
                $table->text('telepon')->nullable();
                $table->text('email')->nullable();
                $table->text('fax')->nullable();
                $table->text('kodepos')->nullable();
                $table->string('kota')->nullable();
                $table->string('status')->default(1);
                $table->integer('uid')->nullable();
                // $table->foreign('uid')->references('id')->on('users');
                $table->timestamps();
            });
        }




        if (!Schema::hasTable('ref_satker')) {
            Schema::create('ref_satker', function (Blueprint $table) {
                $table->string('kddept');
                $table->string('kdbaes1');
                $table->string('kdkppn');
                $table->string('kdsatker')->unique();
                $table->string('nmsatker')->default(0);
                $table->string('kdkewenangan')->nullable();
                $table->text('alamat')->nullable();
                $table->text('telepon')->nullable();
                $table->text('email')->nullable();
                $table->text('fax')->nullable();
                $table->text('kodepos')->nullable();
                $table->text('kota')->nullable();
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
        Schema::dropIfExists('ref_uappaw');
        Schema::dropIfExists('ref_kppn');
        Schema::dropIfExists('ref_kanwil');
        Schema::dropIfExists('ref_kpknl');
        Schema::dropIfExists('ref_kanwildjkn');
        Schema::dropIfExists('ref_es1');
        Schema::dropIfExists('ref_dept');
        Schema::dropIfExists('ref_wilayah');
    }
}
