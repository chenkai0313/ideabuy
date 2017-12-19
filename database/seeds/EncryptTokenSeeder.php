<?php

use Illuminate\Database\Seeder;

class EncryptTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', 'iPhone app', 'Ue75Vtmwvhxwr6qf', 'rsa_public_key.pem', '1'],
            ['2', 'android app', '5B8sNSP3hedG3LuB', 'rsa_public_key_android.pem', '1'],
        ];

        $field = ['id','name','token','publickey_path','is_used'];

        DB::table('encrypt_token')->insert(sql_batch_str($field,$data));
    }
}
