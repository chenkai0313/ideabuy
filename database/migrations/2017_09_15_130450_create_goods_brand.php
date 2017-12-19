<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsBrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_brand', function(Blueprint $table)
        {
            $table->integer('brand_id', true)->comment('主键ID');
            $table->string('brand_name', 20)->default('')->comment('品牌名称');
            $table->string('brand_thumb', 255)->nullable()->default('')->comment('品牌缩略图');
            $table->string('brand_desc', 255)->nullable()->default('')->comment('品牌描述');
            $table->boolean('is_show')->nullable()->default(1)->comment('是否显示（0否，1是）');
            $table->timestamps();
            $table->comment = '商品品牌表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_brand');
    }
}
