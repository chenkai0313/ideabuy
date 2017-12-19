<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function(Blueprint $table)
        {
            $table->integer('goods_id', true)->comment('主键ID');
            $table->string('goods_sn', 60)->default('')->comment('商品编号');
            $table->string('goods_name', 30)->default('')->comment('商品名称');
            $table->mediumInteger('cat_id')->default(0)->comment('商品分类ID');
            $table->mediumInteger('brand_id')->default(0)->comment('商品品牌ID');
            $table->mediumInteger('type_id')->default(0)->comment('商品类型ID');
            $table->mediumInteger('goods_number')->nullable()->default(0)->comment('商品库存');
            $table->decimal('market_price', 10, 4)->default(0.0000)->comment('市场价');
            $table->decimal('goods_price', 10, 4)->default(0.0000)->comment('平台价');
            $table->string('keywords', 60)->nullable()->default('')->comment('商品关键词');
            $table->string('goods_thumb', 255)->nullable()->default('')->comment('商品缩略图（小）');
            $table->string('goods_img', 255)->nullable()->default('')->comment('商品缩略图（大）');
            $table->string('shipping_range', 9)->nullable()->default('')->comment('配送范围');
            $table->boolean('is_index')->nullable()->default(0)->comment('是否首页显示（0否，1是）');
            $table->boolean('is_real')->default(1)->comment('0虚拟商品，1实物商品');
            $table->boolean('is_on_sale')->default(0)->comment('是否上架（0否，1是）');
            $table->boolean('is_shipping')->default(0)->comment('是否包邮（0否，1是）');
            $table->mediumInteger('sort')->nullable()->default(0)->comment('排序（从大到小）');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '商品表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods');
    }
}
