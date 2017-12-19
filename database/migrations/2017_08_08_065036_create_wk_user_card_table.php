<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkUserCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_card', function(Blueprint $table)
		{
			$table->increments('card_id');
			$table->integer('user_id')->index('user_id')->comment('会员ID');
			$table->string('card_number', 19)->default('')->comment('银行卡号');
			$table->string('card_address', 32)->nullable()->default('')->comment('开户所在地');
			$table->string('card_mobile', 11)->default('')->comment('绑卡的预留手机号');
            $table->string('bank_id', 50)->nullable()->default('')->comment('银行id bank_info 表主键');
            $table->string('jl_bind_id', 50)->nullable()->default('')->comment('嘉联绑定银行卡ID');
			$table->timestamp('created_at')->nullable();
			$table->softDeletes();
            $table->engine = 'MyISAM';
            $table->comment = '用户绑定的银行卡表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_card');
	}

}
