<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWkRoleAdminTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('role_admin', function(Blueprint $table)
		{
			$table->foreign('admin_id', 'role_admin_admin_id_foreign')->references('admin_id')->on('admins')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('role_id', 'role_user_role_id_foreign')->references('id')->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('role_admin', function(Blueprint $table)
		{
			$table->dropForeign('role_admin_admin_id_foreign');
			$table->dropForeign('role_user_role_id_foreign');
		});
	}

}
