<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemBankInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_bank_info', function(Blueprint $table)
		{
			$table->integer('bank_id', true);
			$table->string('bank_code',20)->default('')->comment('银行代码');
			$table->string('bank_logo')->nullable()->default('')->comment('银行logo oss url');
			$table->string('bank_name')->nullable()->default('')->comment('银行名字');
			$table->string('color_start')->nullable()->default('')->comment('渐变色开始');
			$table->string('color_stop')->nullable()->default('')->comment('渐变色停止');
			$table->string('bank_line_code')->nullable()->default('')->comment('银行行别代码快付通');
            $table->comment = '银行信息表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_bank_info');
	}

}
