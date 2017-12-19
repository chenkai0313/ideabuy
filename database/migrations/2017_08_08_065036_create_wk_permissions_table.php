<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->unique('permissions_name_unique');
			$table->string('display_name')->nullable()->default('');
			$table->string('description')->nullable()->default('');
			$table->integer('pid')->nullable()->default(0)->comment('父级ID');
			$table->boolean('level')->nullable()->default(1)->comment('栏目所属层级');
            $table ->string('path')->default('')->comment('页面url');
            $table ->boolean('show')->default(0)->comment('是否显示（0否，1是）');
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
		Schema::drop('permissions');
	}

}
