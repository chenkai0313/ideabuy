<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWkUserAccountLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_account_log')){
            Schema::create('user_account_log', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('user_id')->index('user_id')->comment('会员ID');
                $table->decimal('money', 10, 4)->nullable()->default(0.0000)->comment('消费金额');
                $table->boolean('type')->nullable()->default('1')->comment('收支类型（1白条，2支付宝，3微信，4银行卡，5余额）');
                $table->boolean('status')->nullable()->default('-1')->comment('状态（-1.无效，1支出，2收入）');
                $table->timestamps();
                $table->comment = '用户钱包详细表';
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_account_log');
    }
}
