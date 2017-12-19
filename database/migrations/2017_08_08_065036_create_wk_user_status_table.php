<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_status', function(Blueprint $table)
		{
			$table->integer('status_id', true);
			$table->integer('user_id')->index('user_id');
			$table->boolean('is_linkman')->nullable()->default(0)->comment('0未添加，1添加常用联系人');
			$table->boolean('is_idcard')->nullable()->default(0)->comment('0未添加，1添加(身份证信息)');
			$table->boolean('is_idcard_img')->nullable()->default(0)->comment('0未添加，1添加（身份证图片）');
			$table->boolean('is_activate')->nullable()->default(0)->comment('0未激活，1激活（激活白条）');
			$table->tinyInteger('status')->nullable()->default(0)->comment('用户审核状态,1未审核,2审核通过,3不通过审核');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_status');
	}

}
