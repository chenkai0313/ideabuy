<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserWalletTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_wallet', function(Blueprint $table)
		{
			$table->increments('wallet_id');
			$table->integer('user_id')->unique('user_id')->comment('会员ID');
			$table->decimal('user_money', 10, 4)->nullable()->default(0.0000)->comment('可用钱包余额');
			$table->decimal('frozen_money', 10, 4)->nullable()->default(0.0000)->comment('被冻结金额');
			$table->decimal('white_money', 10, 4)->nullable()->default(0.0000)->comment('可用白条余额');
			$table->timestamps();
			$table->comment = '用户钱包表';
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_wallet');
	}

}
