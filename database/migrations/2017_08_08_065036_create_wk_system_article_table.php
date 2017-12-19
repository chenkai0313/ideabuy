<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWkSystemArticleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_article', function(Blueprint $table)
		{
			$table->integer('article_id', true);
			$table->boolean('type_id')->default(1)->index('type_id')->comment('文章类型');
			$table->text('article_title', 65535)->comment('文章标题');
			$table->text('article_content', 65535)->comment('文章内容');
			$table->integer('admin_id')->default(0)->comment('管理员ID');
            $table->mediumInteger('sort')->default(0)->comment('排序（从大到小）');
			$table->timestamps();
			$table->softDeletes();
            $table->comment = '文章管理表';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_article');
	}

}
