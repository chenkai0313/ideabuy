<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkPayLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pay_log', function(Blueprint $table)
		{
			$table->mediumInteger('log_id', true);
			$table->string('order_sn', 64)->nullable()->default('')->comment('订单编号');
			$table->decimal('pay_money', 10, 2)->nullable()->default(0.00)->comment('支付金额');
			$table->boolean('pay_id')->nullable()->default(1)->comment('支付方式（1白条，2支付宝，3微信，4银联）');
			$table->boolean('from_type')->nullable()->default(1)->comment('来源（1订单）');
			$table->string('trade_no', 64)->nullable()->default('')->comment('第三方交易号');
			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
            $table->comment = '支付记录表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pay_log');
	}

}
