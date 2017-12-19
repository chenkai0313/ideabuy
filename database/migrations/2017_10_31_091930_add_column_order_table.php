<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_info', function (Blueprint $table)
        {
            $table->string('shipping_name',32)->nullable()->default('')->comment('物流公司')->after('order_amount');
            $table->string('shipping_time',32)->nullable()->default('')->comment('配送时间')->after('shipping_name');
            $table->string('invoice_type',4)->nullable()->default('')->comment('发票类型(默认0，1个人，2单位)')->after('shipping_time');
            $table->string('invoice_title',32)->nullable()->default('')->comment('发票抬头')->after('invoice_type');
            $table->string('invoice_code',32)->nullable()->default('')->comment('纳税人识别号')->after('invoice_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_info', function (Blueprint $table)
        {
            $table->dropColumn('shipping_name');
            $table->dropColumn('shipping_time');
            $table->dropColumn('invoice_type');
            $table->dropColumn('invoice_title');
            $table->dropColumn('invoice_code');
        });
    }
}
