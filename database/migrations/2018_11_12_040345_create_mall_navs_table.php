<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallNavsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_navs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',10)->comment('名称');
            $table->string('img_url',300)->comment('图片链接');
            $table->tinyInteger('sid')->comment('上层id，0为父id');
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
        Schema::dropIfExists('mall_navs');
    }
}
