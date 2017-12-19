<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_storage', function(Blueprint $table)
        {
            $table->integer('storage_id', true)->comment('主键ID');
            $table->integer('goods_id')->default(0)->comment('商品ID');
            $table->integer('product_id')->default(0)->comment('货品ID');
            $table->integer('storage_number')->comment('库存');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '商品库存表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_storage');
    }
}
