<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_desc', function(Blueprint $table)
        {
            $table->integer('desc_id', true)->comment('主键ID');
            $table->integer('goods_id')->comment('商品ID');
            $table->text('goods_desc', 65535)->comment('商品详情');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '商品描述表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_desc');
    }
}
