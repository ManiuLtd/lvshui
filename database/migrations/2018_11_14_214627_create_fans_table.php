<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid',28)->default('')->comment('openid');
            $table->string('unionid',100)->default('')->comment('unionid');
            $table->string('nickname',50)->default('')->nullable()->comment('昵称');
            $table->string('headimgurl',512)->default('')->nullable()->comment('头像');
            $table->tinyInteger('sex')->default(0)->nullable()->comment('性别');
            $table->string('city',20)->default('')->nullable()->comment('城市');
            $table->string('province',20)->default('')->nullable()->comment('省份');
            $table->string('country',20)->default('')->nullable()->comment('国家');
            $table->string('language',20)->default('')->nullable()->comment('语言');
            $table->string('privilege',256)->default('')->nullable()->comment('特权');
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
        Schema::dropIfExists('fans');
    }
}
