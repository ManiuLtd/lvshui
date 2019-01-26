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
            $table->string('cover', 512)->default('')->comment('封面');
            $table->longText('content')->comment('详细描述');
            $table->string('remark',300)->commend('备注');
            $table->tinyInteger('total')->comment('每日数量');
            $table->tinyInteger('limit')->comment('用户每日购买上限');
            $table->tinyInteger('daily_inventory')->comment('每日库存');
            $table->tinyInteger('price')->comment('价格');
            $table->tinyInteger('is_up')->default(1)->comment('是否上架,1表是，0表否');
            $table->tinyInteger('from_now')->comment('限制天数');
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
