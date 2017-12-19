<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserWalletDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_wallet_detail', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id')->index('user_id')->comment('会员ID');
            $table->decimal('change_money', 10, 4)->default(0.0000)->comment('变动金额(正数表示收入,负数表示支出)');
            $table->decimal('surplus_money', 10, 4)->default(0.0000)->comment('剩余余额');
            $table->decimal('surplus_white_money', 10, 4)->default(0.0000)->comment('剩余白条余额');
            $table->boolean('type')->nullable()->default(1)->comment('收支类型（1白条，2支付宝，3微信，4银行卡，5余额）');
            $table->decimal('status', 10, 0)->default(0.0000)->comment('状态（-1.无效，1支出，2收入）');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '用户钱包详细表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_wallet_detail');
	}

}
