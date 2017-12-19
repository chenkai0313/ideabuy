<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserThirdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_third', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id' )->comment('会员ID');
			$table->string('openid', 28)->nullable()->default('');
			$table->boolean('type')->nullable()->default(1)->comment('类型（1微信，2QQ）');
			$table->string('access_token', 107)->nullable()->default('');
			$table->string('refresh_token', 107)->nullable()->default('');
			$table->string('jpush_token', 107)->nullable()->default('')->comment('设备唯一标识：registration_id');
			$table->string('code', 32)->nullable()->default('');
			$table->timestamps();
            $table->engine = 'MyISAM';
			$table->comment = '用户第三方绑定表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_third');
	}

}
