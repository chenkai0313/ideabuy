<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkAdminsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function(Blueprint $table)
            {
                $table->increments('admin_id')->unique('admin_id');
                $table->string('admin_name', 12)->nullable()->default('')->comment('账号');
                $table->string('admin_nick', 12)->nullable()->default('')->comment('昵称');
                $table->boolean('admin_sex')->nullable()->default(1)->comment('性别（0保密，1男，2.女）');
                $table->string('admin_password', 60)->comment('密码');
                $table->date('admin_birthday')->nullable()->comment('生日');
                $table->string('remember_token', 100)->nullable()->default('');
                $table->boolean('is_super')->nullable()->default(0)->comment('是否超级管理员（0否，1是）');
                $table->timestamps();
                $table->softDeletes();
                $table->comment = '管理员表';
            });
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('admins');
	}

}
