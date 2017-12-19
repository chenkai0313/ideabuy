<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOrderInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_info', function(Blueprint $table)
        {
            $table->integer('supplier_id')->nullable()->default(0)->comment('供应商ID')->after('white_is_pay_off');
            $table->integer('parent_id')->nullable()->default(0)->comment('所属的订单ID，未拆单时为0')->after('supplier_id');
            $table->timestamp('assign_at')->nullable()->comment('平台指派给供应商时间')->after('parent_id');
            $table->timestamp('send_at')->nullable()->comment('供应商发货给客户时间')->after('assign_at');
            $table->timestamp('receive_at')->nullable()->comment('客户确认收货时间')->after('send_at');
            $table->timestamp('apart_at')->nullable()->comment('拆分订单时间')->after('receive_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_info', function(Blueprint $table)
        {
            $table->dropColumn('supplier_id');
            $table->dropColumn('parent_id');
            $table->dropColumn('assign_at');
            $table->dropColumn('send_at');
            $table->dropColumn('receive_at');
            $table->dropColumn('apart_at');
        });
    }
}
