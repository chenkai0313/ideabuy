<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemAdTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_ad_type', function(Blueprint $table)
		{
			$table->tinyInteger('type_id',true)->unsigned();
			$table->string('type_name', 50)->default('')->comment('广告类型名称');
			$table->string('img_size', 100)->default('')->comment('广告图片大小,例如长,宽');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_ad_type');
	}

}
