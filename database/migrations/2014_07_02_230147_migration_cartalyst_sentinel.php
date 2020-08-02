<?php

/**
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel
 * @version    2.0.17
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrationCartalystSentinel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if (!Schema::hasTable('activations')) {
            Schema::create('activations', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('code');
                $table->boolean('completed')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->engine = 'InnoDB';
            });
        }

        if (!Schema::hasTable('persistences')) {
            Schema::create('persistences', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('code');
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->unique('code');
            });
        }

        if (!Schema::hasTable('reminders')) {
            Schema::create('reminders', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('code');
                $table->boolean('completed')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->engine = 'InnoDB';
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('slug');
                $table->string('name');
                $table->string('group_id')->default(0);
                $table->string('permissions', 4000)->nullable();
                $table->string('admin_of', 4000)->nullable();
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->unique('slug');
            });
        }

        if (!Schema::hasTable('role_users')) {
            Schema::create('role_users', function (Blueprint $table) {
                $table->integer('user_id')->unsigned();
                $table->integer('role_id')->unsigned();
                $table->string('user_groups', 4000)->nullable();

                $table->nullableTimestamps();

                $table->engine = 'InnoDB';
                $table->primary(['user_id', 'role_id']);
            });
        }

        if (!Schema::hasTable('throttle')) {
            Schema::create('throttle', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('type');
                $table->string('ip')->nullable();
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username');
                $table->string('email');
                $table->string('password');
                $table->string('permissions', 4000)->nullable();
                $table->timestamp('last_login')->nullable();
                $table->string('nama')->nullable();
                $table->string('nip')->nullable();
                $table->string('telp')->nullable();
                $table->string('alamat', 4000)->nullable();
                $table->string('aktif')->default('1');
                $table->string('hpassword')->nullable();
                $table->string('login_token')->nullable();
                $table->dateTime('logout')->nullable();

                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->unique('username');
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
        Schema::dropIfExists('activations');
        Schema::dropIfExists('persistences');
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_users');
        Schema::dropIfExists('throttle');
        Schema::dropIfExists('users');
        
    }
}
