<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier', function(Blueprint $table)
        {
            $table->integer('supplier_id', true)->unique('supplier_id')->comment('主键ID');
            $table->string('supplier_mobile', 11)->default('')->unique('supplier_mobile')->comment('手机号');
            $table->string('supplier_name', 20)->default('')->comment('供应商名称');
            $table->string('supplier_password', 60)->comment('密码');
            $table->string('province',9)->default('')->comment('省');
            $table->string('city',9)->default('')->comment('市');
            $table->string('district',9)->default('')->comment('区');
            $table->string('address', 64)->nullable()->default('')->comment('详细地址');
            $table->string('login_ip', 64)->nullable()->default('')->comment('最后登录ip');
            $table->timestamp('login_at')->nullable()->comment('最后登录时间');
            $table->string('remark', 64)->nullable()->default('')->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->comment = '供应商表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('supplier');
    }
}
