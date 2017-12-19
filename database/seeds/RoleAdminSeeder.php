<?php

use Illuminate\Database\Seeder;

class RoleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            ['1', '1'],
        ];

        $field = ['admin_id','role_id'];

        DB::table('role_admin')->insert(sql_batch_str($field,$data));
    }
}
