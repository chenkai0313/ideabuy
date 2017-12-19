<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkAdminLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_log', function(Blueprint $table)
		{
			$table->integer('log_id', true)->comment('主键ID');
			$table->string('admin_name', 30)->comment('管理员名称');
			$table->integer('admin_id')->comment('管理员ID');
            $table->string('operate_target', 50)->comment('操作模块');
			$table->string('operate_ip', 30)->nullable()->default('')->comment('操作ip');
			$table->longtext('operate_content', 65535)->nullable()->comment('日志记录内容');
			$table->dateTime('operate_time')->nullable()->comment('操作时间');
			$table->boolean('operate_status')->comment('操作状态：1成功，2失败');
			$table->text('remark', 65535)->nullable()->comment('备注');
            $table->engine = 'MyISAM';
            $table->comment = '后台操作日志表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_log');
	}

}
