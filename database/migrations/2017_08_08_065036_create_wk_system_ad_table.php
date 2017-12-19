<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemAdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_ad', function(Blueprint $table)
		{
			$table->tinyInteger('ad_id',true)->unsigned();
			$table->tinyInteger('type_id')->index('type_id')->comment('ad_type的主键');
			$table->string('ad_img')->nullable()->default('')->comment('广告图片');
			$table->tinyInteger('is_show')->default(1)->comment('是否显示（0否，1是）');
            $table->string('location_href')->nullable()->default('')->comment('广告指向的url');
            $table->mediumInteger('sort')->default(0)->comment('排序（从大到小）');
			$table->softDeletes();
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
		Schema::drop('system_ad');
	}

}
