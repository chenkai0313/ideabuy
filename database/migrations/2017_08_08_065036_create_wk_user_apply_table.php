<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_apply', function(Blueprint $table)
		{
			$table->integer('apply_id', true);
			$table->integer('user_id')->default(0)->unique('user_id')->comment('会员ID');
			$table->string('real_name', 30)->nullable()->default('')->comment('真实姓名');
			$table->string('user_idcard', 18)->nullable()->default('身份证号');
			$table->string('id_img', 30)->nullable()->default('')->comment('以\',\'隔开的file表的ID');
			$table->tinyInteger('status')->nullable()->default(1)->comment('审核状态,1为未审核,2为审核通过,3为审核不通过');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
			$table->string('reason', 100)->nullable()->default('')->comment('理由');
			$table->boolean('apply_type')->nullable()->default(0)->comment('申请类型,1为用户审核');
            $table->comment = '用户申请表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_apply');
	}

}
