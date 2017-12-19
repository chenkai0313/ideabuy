<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemMsgTemplateInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_msg_template_info', function(Blueprint $table)
		{
			$table->integer('keyword_id', true)->comment('类型ID');
			$table->string('keyword_name')->nullable()->default('')->comment('短信关键字');
            $table->string('keyword_zh')->nullable()->default('')->comment('关键字中文注释');
			$table->timestamps();
            $table->engine = 'InnoDB';
            $table->comment = '短信模板关键字表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('system_msg_template_info');
	}

}
