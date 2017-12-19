<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemArticleTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_article_type', function(Blueprint $table)
		{
			$table->integer('type_id', true)->index('index');
			$table->string('type_name', 12)->comment('类型名称');
			$table->boolean('parent_id')->nullable()->default(0)->comment('父级ID');
            $table->tinyInteger('level')->default(0)->comment('层级');
			$table->timestamp('created_at')->nullable();
            $table->comment = '文章类型表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_article_type');
	}

}
