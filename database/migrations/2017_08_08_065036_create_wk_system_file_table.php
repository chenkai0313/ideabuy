<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_file', function(Blueprint $table)
		{
			$table->increments('file_id')->comment('文件ID');
			$table->string('file_path')->default('')->comment('路径');
			$table->boolean('file_type')->unsigned()->default(1)->comment('类型（1.身份证）');
			$table->integer('user_id')->comment('会员ID');
			$table->timestamp('created_at')->nullable();
			$table->softDeletes();
            $table->engine = 'MyISAM';
            $table->comment = '图片附件表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_file');
	}

}
