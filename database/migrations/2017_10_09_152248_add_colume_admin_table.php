<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumeAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #admins表
        Schema::table('admins', function(Blueprint $table)
        {
            $table->string('province',9)->default('')->comment('省')->after('is_super');
            $table->string('city',9)->default('')->comment('市')->after('province');
            $table->string('district',9)->default('')->comment('区')->after('city');
            $table->string('address', 64)->nullable()->default('')->comment('详细地址')->after('district');
            $table->string('login_ip', 64)->nullable()->default('')->comment('最后登录ip')->after('address');
            $table->timestamp('login_at')->nullable()->comment('最后登录时间')->after('login_ip');
            $table->string('remark', 64)->nullable()->default('')->comment('备注')->after('login_at');
        });
        #goods表
        Schema::table('goods', function(Blueprint $table)
        {
            $table->integer('admin_id')->comment('供应商ID')->after('sort');
        });
        #goods_products表
        Schema::table('goods_products', function(Blueprint $table)
        {
            $table->integer('admin_id')->comment('供应商ID')->after('sort');
        });
        #order_info表
        Schema::table('order_info', function(Blueprint $table)
        {
            $table->integer('admin_id')->comment('供应商ID')->after('user_id');
        });
        #goods_cart表
        Schema::table('goods_cart', function(Blueprint $table)
        {
            $table->integer('admin_id')->comment('供应商ID')->after('user_id');
        });
        #goods_brand表
        Schema::table('goods_brand', function(Blueprint $table)
        {
            $table->softDeletes();
        });
        #goods_category表
        Schema::table('goods_category', function(Blueprint $table)
        {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function(Blueprint $table)
        {
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('district');
            $table->dropColumn('address');
            $table->dropColumn('login_ip');
            $table->dropColumn('login_at');
            $table->dropColumn('remark');
        });
        Schema::table('goods', function(Blueprint $table)
        {
            $table->dropColumn('admin_id');
        });
        Schema::table('goods_products', function(Blueprint $table)
        {
            $table->dropColumn('admin_id');
        });
        Schema::table('order_info', function(Blueprint $table)
        {
            $table->dropColumn('admin_id');
        });
        Schema::table('goods_cart', function(Blueprint $table)
        {
            $table->dropColumn('admin_id');
        });
        Schema::table('goods_brand', function(Blueprint $table)
        {
            $table->dropColumn('deleted_at');
        });
        Schema::table('goods_category', function(Blueprint $table)
        {
            $table->dropColumn('deleted_at');
        });
    }
}
