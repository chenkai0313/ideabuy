<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', 'admin', '超级管理员', '系统管理员，拥有所有权限', '2017-07-27 14:11:09', '2017-08-01 14:46:06', null],
        ];

        $field = ['id','name','display_name','description','created_at','updated_at','deleted_at'];

        DB::table('roles')->insert(sql_batch_str($field,$data));

    }
}
