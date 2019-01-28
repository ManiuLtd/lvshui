<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFanTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fan_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fan_id')->commend('粉丝id');
            $table->integer('ticket_id')->commend('门票id');
            $table->string('name', 20)->default('')->comment('真实姓名');
            $table->string('mobile')->default('')->comment('手机号码');
            $table->integer('purchase_quantity')->comment('购买数量');
            $table->dateTime('booking_date')->comment('预约日期');
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
        Schema::dropIfExists('fan_tickets');
    }
}
