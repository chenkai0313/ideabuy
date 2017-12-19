<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_category', function(Blueprint $table)
        {
            $table->integer('cat_id', true)->comment('主键ID');
            $table->mediumInteger('pid')->default(0)->comment('父级ID');
            $table->mediumInteger('sort_order')->nullable()->default(0)->comment('排序ID');
            $table->string('cat_name', 60)->default('')->comment('分类名称');
            $table->string('cat_desc', 255)->nullable()->default('')->comment('分类描述');
            $table->string('cat_thumb', 255)->nullable()->default('')->comment('分类缩略图');
            $table->string('keywords', 255)->nullable()->default('')->comment('分类关键词');
            $table->boolean('is_show')->nullable()->default(1)->comment('是否显示（0否，1是）');
            $table->boolean('is_show_nav')->nullable()->default(1)->comment('是否导航显示（0否，1是）');
            $table->timestamps();
            $table->comment = '商品分类表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_category');
    }
}
