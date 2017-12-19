<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemSmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_sms', function(Blueprint $table)
		{
			$table->increments('sms_id');
			$table->string('mobile', 11)->default('')->comment('手机号');
			$table->string('code', 6)->default('')->comment('验证码');
			$table->boolean('status')->default(0)->comment('是否已使用（0否，1是）');
			$table->boolean('type')->default(1)->comment('短信类型（1注册，2找回密码,3重置交易密码,4绑定银行卡,5绑定新手机号,6登陆后重置登录密码）');
			$table->timestamp('created_at')->nullable();
            $table->engine = 'MyISAM';
            $table->comment = '短信验证码表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_sms');
	}

}
