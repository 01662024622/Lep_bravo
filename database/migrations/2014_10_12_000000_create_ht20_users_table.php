<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHT20UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ht20_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avata')->default("/user.png");
            $table->string('tagname')->unique();
            $table->string('position');
            $table->integer('apartment_id')->nullable();
            $table->string('location')->nullable();
            $table->string('skype')->nullable();
            $table->string('email_htauto')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_htauto')->nullable();
            $table->date('birth_day')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('authentication')->unique();
            $table->integer('role')->default(50);
            $table->integer('status')->default(0);
            $table->bigInteger('create_by')->default(0);
            $table->bigInteger('modify_by')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('ht20_users');
    }
}
