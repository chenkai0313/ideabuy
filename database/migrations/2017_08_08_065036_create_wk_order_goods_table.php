<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('order_goods', function(Blueprint $table)
        {
            $table->mediumInteger('id', true);
            $table->string('order_sn', 64)->default('')->index('order_sn')->comment('订单编号');
            $table->string('goods_key', 16)->default('')->index('goods_key')->comment('订单商品唯一码');
            $table->mediumInteger('goods_id')->nullable()->default(0)->comment('商品ID');
            $table->mediumInteger('product_id')->nullable()->default(0)->comment('货品ID');
            $table->string('goods_name', 32)->nullable()->default('')->comment('商品名');
            $table->string('goods_thumb', 128)->nullable()->default('')->comment('商品缩略图');
            $table->string('attr_name', 64)->nullable()->default('')->comment('属性名,以|隔开');
            $table->string('attr_value', 64)->nullable()->default('')->comment('属性值,以|隔开');
            $table->string('goods_sn')->nullable()->default('')->comment('商品编号');
            $table->decimal('product_price', 10)->nullable()->default(0.00)->comment('货品价');
            $table->decimal('market_price', 10)->nullable()->default(0.00)->comment('市场价');
            $table->integer('goods_number')->nullable()->default(0)->comment('下单数量');
            $table->decimal('goods_amount', 10)->nullable()->default(0.00)->comment('商品合计');
            $table->string('goods_unit', 8)->nullable()->default('')->comment('商品单位（中文）');
            $table->comment = '订单附表(订单商品表)';
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_goods');
	}

}
