<?php

use Illuminate\Database\Seeder;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', 'admin', '昵称', '1', '$2y$10$4IP0fVmANpj3jGdFVoERAugkqAYeQjmYn6WjC/ha/6XBlXXxrTzfu', null, null, '1', '2017-07-25 17:11:30', '2017-08-11 13:05:36', null],
        ];

        $field = ['admin_id','admin_name','admin_nick','admin_sex','admin_password','admin_birthday','remember_token','is_super','created_at','updated_at','deleted_at'];

        DB::table('admins')->insert(sql_batch_str($field,$data));
    }
}
