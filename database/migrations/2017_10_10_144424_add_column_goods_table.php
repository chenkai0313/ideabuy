<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods', function (Blueprint $table)
        {
            $table->integer('sales_number')->default(0)->comment('商品销量')->after('sort');
            $table->integer('comment_number')->default(0)->comment('商品评论数量')->after('sales_number');
            $table->string('goods_subname', 32)->nullable()->default('')->comment('商品副标题')->after('goods_name');
            $table->decimal('comment_star',10,2)->default(0.00)->comment('评论星级')->after('comment_number');
        });
        Schema::table('admins', function (Blueprint $table)
        {
            $table->string('admin_mobile', 11)->nullable()->default('')->comment('手机')->after('admin_birthday');
        });
        Schema::table('system_ad', function (Blueprint $table)
        {
            $table->integer('cat_id')->default(0)->comment('商品分类ID')->after('ad_img');
            $table->integer('brand_id')->default(0)->comment('商品品牌ID')->after('cat_id');
            $table->integer('goods_id')->default(0)->comment('商品ID')->after('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods', function (Blueprint $table)
        {
            $table->dropColumn('sales_number');
            $table->dropColumn('comment_number');
            $table->dropColumn('goods_subname');
            $table->dropColumn('comment_star');
        });
        Schema::table('admins', function (Blueprint $table)
        {
            $table->dropColumn('admin_mobile');
        });
        Schema::table('system_ad', function (Blueprint $table)
        {
            $table->dropColumn('cat_id');
            $table->dropColumn('brand_id');
            $table->dropColumn('goods_id');
        });
    }
}
