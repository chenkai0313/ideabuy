<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkEncryptTokenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('encrypt_token', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->string('name', 128)->nullable()->default('')->comment('项目名');
            $table->string('token', 128)->nullable()->default('')->comment('token值');
            $table->string('publickey_path')->nullable()->default('')->comment('公钥路径');
            $table->boolean('is_used')->default(1)->comment('是否适用');
            $table->engine = 'MyISAM';
            $table->comment = 'RSA加密、解密秘钥';
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('encrypt_token');
	}

}
