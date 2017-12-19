<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkOrderInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_info', function(Blueprint $table)
		{
			$table->integer('order_id', true);
			$table->integer('user_id')->comment('会员ID');
			$table->string('order_sn', 50)->nullable()->default('')->unique('order_sn')->comment('订单编号');
			$table->boolean('pay_id')->default(1)->comment('支付方式（1白条，2支付宝，3微信，4银行卡，5余额）');
			$table->boolean('order_status')->nullable()->default(0)->comment('订单状态（0未付款，1已付款待发货，2已付款待收货，3确认收货，4已取消，5已完成）');
			$table->boolean('is_comment')->nullable()->default(0)->comment('是否评价');
			$table->decimal('goods_amount', 10)->nullable()->default(0.00)->comment('总商品价格，sum（数量*单价）');
			$table->decimal('freight_amount', 10)->nullable()->default(0.00)->comment('运费');
			$table->decimal('order_amount', 10)->nullable()->default(0.00)->comment('实收金额');
			$table->string('consignee', 20)->nullable()->default('')->comment('收货人名字');
			$table->string('province',9)->default('')->comment('收货人省');
			$table->string('city',9)->default('')->comment('收货人市');
			$table->string('district',9)->default('')->comment('收货人区');
			$table->string('street',9)->nullable()->default('')->comment('收货地址街道');
			$table->string('address', 64)->nullable()->default('')->comment('收货人详细地址');
			$table->string('mobile', 11)->nullable()->default('')->comment('收货人手机');
			$table->boolean('order_from')->default(0)->comment('订单来源终端（1线上商城，2线下门店）');
			$table->boolean('loan_product_id')->default(0)->comment('金融产品类型(1分期，2不分期）');
			$table->boolean('month')->nullable()->default(0)->comment('分期期数');
			$table->string('order_remark', 64)->nullable()->default('')->comment('订单备注');
            $table->boolean('white_is_pay_off')->default(0)->comment('白条消费的不分期的订单在当期是否还清（0否，1是）');
			$table->timestamps();
			$table->softDeletes();
            $table->comment = '订单主表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_info');
	}

}
