<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierGoodsStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_goods_storage', function(Blueprint $table)
        {
            $table->integer('id', true)->comment('主键ID');
            $table->integer('supplier_id')->default(0)->comment('供应商ID');
            $table->integer('goods_id')->default(0)->comment('商品ID');
            $table->integer('product_id')->default(0)->comment('货品ID');
            $table->integer('storage_number')->comment('库存');
            $table->decimal('market_price', 10, 4)->nullable()->default(0.0000)->comment('市场价');
            $table->decimal('product_price', 10, 4)->nullable()->default(0.0000)->comment('平台价');
            $table->decimal('import_price', 10, 4)->nullable()->default(0.0000)->comment('进货价');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '供应商商品表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('supplier_goods_storage');
    }
}
