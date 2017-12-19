<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserAddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_address', function(Blueprint $table)
		{
			$table->increments('address_id');
			$table->integer('user_id')->comment('会员ID');
            $table->string('consignee',16)->nullable()->default('')->comment('收货人姓名');
            $table->string('mobile',11)->nullable()->default('')->comment('收货人手机');
			$table->string('province', 9)->default('')->comment('省');
			$table->string('city', 9)->default('')->comment('市');
			$table->string('district', 9)->default('')->comment('区');
			$table->string('street',9)->nullable()->default('')->comment('街道');
			$table->string('address', 64)->default('')->comment('详细地址');
			$table->timestamps();
            $table->engine = 'MyISAM';
			$table->comment = '用户收货地址信息表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_address');
	}

}
