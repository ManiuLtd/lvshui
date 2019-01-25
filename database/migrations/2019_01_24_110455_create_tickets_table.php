<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->comment('名称');
            $table->longText('content')->comment('详细描述');
            $table->string('remark',300)->commend('备注');
            $table->tinyInteger('total')->comment('商品数量');
            $table->tinyInteger('limit')->comment('用户购买上限');
            $table->tinyInteger('price')->comment('价格');
            $table->tinyInteger('is_up')->default(1)->comment('是否上架,1表是，0表否');
            $table->dateTime('start_date')->comment('开始日期');
            $table->dateTime('end_date')->comment('结束日期');
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
        Schema::dropIfExists('tickets');
    }
}
