<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_attribute', function(Blueprint $table)
        {
            $table->integer('attr_id', true)->comment('主键ID');
            $table->mediumInteger('type_id')->default(0)->comment('类型ID');
            $table->string('attr_name', 20)->nullable()->default('')->comment('属性名称');
            $table->mediumInteger('sort')->nullable()->default(0)->comment('排序（从大到小）');
            $table->timestamp('created_at')->nullable();
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
        Schema::drop('goods_attribute');
    }
}
