<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('user_id')->unique('user_id');
			$table->string('real_name', 4)->nullable()->default('')->comment('真实姓名');
			$table->string('salt', 4)->nullable()->default('')->comment('盐值');
			$table->string('user_password', 60)->comment('密码');
			$table->string('user_portrait', 128)->nullable()->default('')->comment('头像');
			$table->string('user_idcard', 18)->nullable()->default('')->comment('身份证号');
			$table->string('user_mobile', 11)->default('')->unique('user_mobile')->comment('手机号');
			$table->string('pay_password', 120)->nullable()->default('')->comment('交易密码');
			$table->mediumInteger('address_id')->nullable()->default(0)->index('address_id')->comment('默认收货地址ID');
			$table->mediumInteger('card_id')->nullable()->default(0)->comment('默认银行卡ID');
			$table->string('qq_openid', 32)->nullable()->default('');
			$table->string('wx_openid', 28)->nullable()->default('');
			$table->string('remember_token', 100)->nullable()->default('');
			$table->smallInteger('credit_point')->nullable()->default(0)->comment('信用积分');
            $table ->string('credit_code',10)->default('')->comment('授信二维码');
			$table->decimal('white_amount', 10, 4)->nullable()->default(0.0000)->comment('白条总额度');
            $table->timestamp('activate_date')->nullable()->comment('白条激活日');
            $table->timestamp('first_bill_date')->nullable()->comment('首次出账日');
            $table->timestamp('first_pay_date')->nullable()->comment('首次还款日');
            $table->string('client_device',64)->default('')->comment('设备（ios,android,pc,wx）');
            $table->string('client_version',64)->default('')->comment('设备版本号');
			$table->boolean('is_black')->nullable()->default(0)->comment('是否是黑名单');
			$table->timestamps();
			$table->softDeletes();
			$table->comment = '用户表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
