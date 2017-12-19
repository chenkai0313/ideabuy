<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemRegionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_region', function(Blueprint $table)
		{
			$table->mediumInteger('region_id', true)->comment('主键ID,自增');
			$table->string('region_code', 9)->nullable()->default('')->unique('region_code')->comment('区域编码');
			$table->string('parent_id', 9)->nullable()->default('')->index('parent_id')->comment('父级编码');
			$table->string('region_name', 50)->nullable()->default('')->comment('区域名称');
			$table->boolean('region_level')->nullable()->default(0)->comment('区域等级');
            $table->comment = '全国省市区街道表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_region');
	}

}
