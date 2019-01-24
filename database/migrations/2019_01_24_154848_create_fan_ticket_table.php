<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFanTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fan_ticket', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('fan_id')->commend('粉丝id');
            $table->tinyInteger('ticket_id')->commend('门票id');
            $table->string('name', 20)->default('')->comment('真实姓名');
            $table->string('mobile')->default('')->comment('手机号码');
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
        Schema::dropIfExists('fan_ticket');
    }
}
