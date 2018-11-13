<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fan_id')->default(0)->comment('粉丝ID');
            $table->string('card_id',30)->default('')->comment('会员卡编号');
            $table->string('name', 20)->default('')->comment('真实姓名');
            $table->string('mobile')->default('')->comment('手机号码');
            $table->integer('integral')->default(0)->comment('可用积分');
            $table->decimal('money', 10, 2)->default(0)->comment('余额');
            $table->timestamp('deadline')->nullable()->comment('有效日期');
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
        Schema::dropIfExists('members');
    }
}
