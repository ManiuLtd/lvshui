<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->comment('名称');
            $table->longText('content')->comment('详细描述');
            $table->integer('total')->comment('商品数量');
            $table->integer('limit')->comment('用户购买上限');
            $table->decimal('price')->comment('价格');
            $table->decimal('discount')->comment('优惠价');
            $table->integer('monthly_sales')->comment('月销量');
            $table->tinyInteger('is_up')->default(1)->comment('是否上架,1表是，0表否');
            $table->dateTime('sratr_date')->comment('生效日期');
            $table->dateTime('end_date')->comment('失效日期');
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
        Schema::dropIfExists('mall_goods');
    }
}
