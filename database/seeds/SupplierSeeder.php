<?php

use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', '17611110000', '供应商管理员', '$10$AXUEyS40bIDouF3mXJ4Ro.ideFJWmu6yeDm2qz/cJ1dUbZM76I2KK', '33', '3302', '330283', '浙江宁波', null , null , null ,'2017-07-25 17:11:30', '2017-08-11 13:05:36', null],
        ];

        $field = ['supplier_id','supplier_mobile','supplier_name','supplier_password','province','city','district','address','login_ip','login_at','remark','created_at','updated_at','deleted_at'];

        DB::table('supplier')->insert(sql_batch_str($field,$data));
    }
}
