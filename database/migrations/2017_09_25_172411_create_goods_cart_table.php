<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_cart', function(Blueprint $table)
        {
            $table->integer('cart_id', true)->comment('主键ID');
            $table->integer('user_id')->comment('会员ID');
            $table->integer('goods_id')->comment('商品ID');
            $table->integer('product_id')->comment('货品ID');
            $table->string('goods_sn', 60)->default('')->comment('商品编号');
            $table->string('goods_name', 30)->default('')->comment('商品名称');
            $table->integer('goods_number')->comment('商品数量');
            $table->string('goods_attr', 60)->default('')->comment('货品属性');
            $table->decimal('market_price', 10, 4)->default(0.0000)->comment('市场价');
            $table->decimal('product_price', 10, 4)->default(0.0000)->comment('平台价');
            $table->string('goods_thumb', 60)->nullable()->default('')->comment('商品缩略图（小）');
            $table->string('goods_img', 60)->nullable()->default('')->comment('商品缩略图（大）');
            $table->mediumInteger('sort')->nullable()->default(0)->comment('排序（从大到小）');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'MyISAM';
            $table->comment = '购物车表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_cart');
    }
}
