<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->commend('类型');
            $table->tinyInteger('fan_id')->commend('粉丝id');
            $table->tinyInteger('good_id')->commend('商品id');
            $table->tinyInteger('pay_state')->default(0)->comment('支付状态，0未支付1已支付-1取消订单');
            $table->tinyInteger('use_state')->default(0)->comment('使用状态，0未使用1已使用-1申请退款-2已退款');
            $table->integer('price')->comment('价格');
            $table->string('code')->comment('验证码');
            $table->dateTime('pay_time')->comment('支付时间');
            $table->dateTime('refund_time')->comment('退款时间');
            $table->string('ps',300)->commend('备注');
            $table->string('refund_reason',300)->commend('退款理由');
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
        Schema::dropIfExists('orders');
    }
}
