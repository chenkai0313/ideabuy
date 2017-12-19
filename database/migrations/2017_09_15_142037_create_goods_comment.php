<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_comment', function(Blueprint $table)
        {
            $table->integer('comment_id', true)->comment('主键ID');
            $table->string('order_sn', 50)->default('')->comment('订单编号');
            $table->string('goods_key', 16)->default('')->comment('订单商品唯一码');
            $table->integer('goods_id')->comment('商品ID');
            $table->integer('product_id')->comment('货品ID');
            $table->integer('user_id')->comment('会员ID');
            $table->boolean('comment_type')->default(1)->comment('评论类型（1商品）');
            $table->tinyInteger('comment_star')->default(0)->comment('评论星级');
            $table->text('comment_pics', 65535)->nullable()->comment('评价图片');
            $table->text('comment_desc', 65535)->nullable()->comment('评论内容');
            $table->text('comment_extra_desc', 65535)->nullable()->comment('追评内容');
            $table->text('comment_repay', 65535)->nullable()->comment('评论回复');
            $table->integer('admin_id')->comment('供应商ID');
            $table->timestamp('repay_at')->nullable()->comment('回复时间');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '商品评论表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_comment');
    }
}
