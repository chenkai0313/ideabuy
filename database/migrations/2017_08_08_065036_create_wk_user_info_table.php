<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_info', function(Blueprint $table)
		{
			$table->increments('info_id');
			$table->integer('user_id')->unique('user_id')->comment('会员ID');
			$table->string('user_education', 10)->charset('utf8')->nullable()->default('')->comment('学历');
			$table->string('user_profession', 10)->charset('utf8')->nullable()->default('')->comment('职位');
			$table->string('user_company')->nullable()->default('');
			$table->string('user_income', 10)->charset('utf8')->nullable()->default('')->comment('月收入');
			$table->string('user_qq', 16)->charset('utf8')->nullable()->default('')->comment('QQ');
			$table->string('user_email')->charset('utf8')->nullable()->default('')->comment('邮箱');
			$table->string('link_man', 4)->charset('utf8')->nullable()->default('')->comment('常用联系人');
			$table->string('link_mobile', 11)->charset('utf8')->nullable()->default('')->comment('联系人电话');
			$table->string('link_relation', 6)->charset('utf8')->nullable()->default('')->comment('与联系人关系');
			$table->string('province', 8)->charset('utf8')->nullable()->default('')->comment('省');
			$table->string('city', 8)->charset('utf8')->nullable()->default('')->comment('市');
			$table->string('district', 8)->charset('utf8')->nullable()->default('')->comment('区');
			$table->string('address', 64)->charset('utf8')->nullable()->default('')->comment('地址');
			$table->timestamps();
            $table->engine = 'MyISAM';
			$table->comment = '用户信息附加表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_info');
	}

}
