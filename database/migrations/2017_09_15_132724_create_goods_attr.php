<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsAttr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_attr', function(Blueprint $table)
        {
            $table->integer('attr_id', true)->comment('主键ID');
            $table->integer('goods_id')->default(0)->comment('商品ID');
            $table->integer('product_id')->default(0)->comment('货品ID');
            $table->string('attr_name', 20)->nullable()->default('')->comment('属性名称');
            $table->string('attr_value', 20)->nullable()->default('')->comment('属性值');
            $table->mediumInteger('sort')->nullable()->default(0)->comment('排序（从大到小）');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '商品属性表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_attr');
    }
}
