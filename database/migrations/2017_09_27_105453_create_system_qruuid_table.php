<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemQruuidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_qruuid', function(Blueprint $table)
        {
            $table->integer('id', true)->comment('主键ID');
            $table->string('qruuid', 15)->default('')->unique('qruuid')->comment('网页二维码无关联qruuid');
            $table->boolean('status')->default(0)->comment('是否已使用（0否，1废弃，2被绑定成功）');
            $table->integer('user_id')->default(0)->comment('绑定的会员ID');
            $table->string('url')->default('')->comment('用户登录前的url');
            $table->text('token')->nullable()->comment('token');
            $table->timestamp('created_at')->nullable();
            $table->comment = '二维码uuid';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_qruuid');
    }
}
