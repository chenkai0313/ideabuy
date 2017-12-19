<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkRoleAdminTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('role_admin', function(Blueprint $table)
		{
			$table->integer('admin_id')->unsigned();
			$table->integer('role_id')->unsigned()->index('role_user_role_id_foreign');
			$table->primary(['admin_id','role_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('role_admin');
	}

}
