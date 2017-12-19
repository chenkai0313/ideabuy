<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemMsgTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_msg_template', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('content', 65535)->nullable()->comment('短信内容');
			$table->string('prepare_node')->nullable()->default('')->comment('预发节点');
            $table->string('msg_tag',20)->nullable()->default('')->comment('短信标签');
            $table->string('msg_type')->nullable()->default('')->comment('消息类型');
            $table->string('msg_title')->nullable()->default('')->comment('消息标题');
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->comment = '短信模板表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('system_msg_template');
	}

}
