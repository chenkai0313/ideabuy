<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemConstantTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_constant', function(Blueprint $table)
		{
			$table->integer('constant_id', true);
			$table->string('constant_code', 50)->default('')->comment('常量code');
			$table->string('constant_content')->default('')->comment('常量内容');
			$table->integer('sort')->nullable()->default(0)->comment('排序');
			$table->integer('type_id')->default(0)->comment('system_constant_type的主键ID');
			$table->boolean('is_img')->nullable()->default(1)->comment('是否是图片,0是,1否');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_constant');
	}

}
