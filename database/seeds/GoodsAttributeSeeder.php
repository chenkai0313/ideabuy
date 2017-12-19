<?php

use Illuminate\Database\Seeder;

class GoodsAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [

        ];
        $field = ['ad_id','type_id','ad_img','is_show','location_href','deleted_at','created_at','updated_at'];
        DB::table('system_ad')->insert(sql_batch_str($field,$data));
    }
}
