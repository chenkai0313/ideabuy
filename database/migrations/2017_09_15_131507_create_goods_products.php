<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_products', function(Blueprint $table)
        {
            $table->integer('product_id', true)->comment('主键ID');
            $table->integer('goods_id')->comment('商品ID');
            $table->string('product_name', 255)->nullable()->default('')->comment('货品名称');
            $table->mediumInteger('product_number')->nullable()->default('0')->comment('货品库存');
            $table->decimal('market_price', 10, 4)->default(0.0000)->comment('市场价');
            $table->decimal('product_price', 10, 4)->default(0.0000)->comment('平台价');
            $table->string('product_sn', 60)->nullable()->default('')->comment('货品编号');
            $table->string('goods_color_value', 20)->nullable()->default('')->comment('颜色的值');
            $table->string('goods_attr', 60)->nullable()->default('')->comment('货品属性');
            $table->string('brand_desc', 255)->nullable()->default('')->comment('品牌描述');
            $table->boolean('is_show')->nullable()->default(1)->comment('是否显示（0否，1是）');
            $table->mediumInteger('sort')->nullable()->default(0)->comment('排序（从大到小）');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '货品表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_products');
    }
}
