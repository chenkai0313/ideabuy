<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemConstantTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_constant_type', function(Blueprint $table)
		{
			$table->integer('type_id', true);
			$table->string('type', 50)->nullable()->default('')->unique('type')->comment('常量类型');
			$table->string('name', 50)->nullable()->default('')->comment('常量名字');
			$table->timestamp('created_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_constant_type');
	}

}
