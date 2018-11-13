<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallSwipersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_swipers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image', 512)->default('')->comment('图片');
            $table->string('url', 512)->default('')->comment('链接');
            $table->tinyInteger('display')->default(0)->comment('是否显示');
            $table->integer('group')->comment('轮播图组');
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
        Schema::dropIfExists('mall_swipers');
    }
}
