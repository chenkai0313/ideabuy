<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWkSystemVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('system_version')){
            Schema::create('system_version',function(Blueprint $table){
                $table->integer('id',true);
                $table->string('device',20)->default('')->index('device')->comment('设备');
                $table->string('version',30)->default('')->index('version')->comment('版本');
                $table->string('version_url')->nullable()->default('')->comment('oss地址');
                $table->string('version_content')->default('')->comment('版本更新内容');
                $table->boolean('update_type')->default(0)->comment('1重新下载（安装的是下发apk包，ios是自己去appstore），2为前端资源，3为热更新');
                $table->boolean('update_mode')->default(2)->comment('更新方式：1为全量更新，2为增量更新');
                $table->string('module')->default('')->comment('模块');
                $table->string('md5')->default('')->comment('md5');
                $table->timestamp('created_at');
                $table->engine = 'InnoDB';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('system_version');
    }
}
