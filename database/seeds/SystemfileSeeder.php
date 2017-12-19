<?php

use Illuminate\Database\Seeder;

class SystemfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['40', 'static/product/ios1_022fb7169f6c5b97eadc5d978add13ad5.png', '1', '2', '2017-08-29 09:32:09', null],
            ['41', 'static/product/ios1_0558e18bb4c30f10eeb2d6c464d165f3a.png', '1', '2', '2017-08-29 09:32:09', null],
            ['42', 'static/product/ios1_0ba1ca8348ba34604e27f88fe8bf4a516.png', '1', '2', '2017-08-29 09:32:09', null],
            ['64', 'static/product/ios1_0e3316926dad28556c825b444f9a4f171.png', '1', '3', '2017-08-29 13:34:33', null],
            ['65', 'static/product/ios1_0460c62770e4c0ac2cea3002f04b0177a.png', '1', '3', '2017-08-29 13:34:33', null],
            ['66', 'static/product/ios1_0ffd2f94521583f4d0e15f63d6cd084aa.png', '1', '3', '2017-08-29 13:34:33', null],
            ['67', 'static/product/ios1_0d113e7e4ad796837d0064ea4e97af46f.png', '1', '4', '2017-08-29 13:40:31', null],
            ['68', 'static/product/ios1_00fa8d57cc3bc0529e46094a84367e0a2.png', '1', '4', '2017-08-29 13:40:31', null],
            ['69', 'static/product/ios1_0e4c4bd7525c87814391c911597693da4.png', '1', '4', '2017-08-29 13:40:31', null],
            ['73', 'static/product/ios1_0409f3ae8e0508aa912c2e4b510544272.png', '1', '5', '2017-08-29 13:43:03', null],
            ['74', 'static/product/ios1_093b6c64a1e45e693822c475331ae834b.png', '1', '5', '2017-08-29 13:43:03', null],
            ['75', 'static/product/ios1_044cbb8799420bb21b71f236556f33f16.png', '1', '5', '2017-08-29 13:43:03', null],
            ['163', 'static/product/ios1_0_8ab26dc0bb5cf7484cdf3c244d2ee652f.png', '1', '6', '2017-09-01 14:03:20', null],
            ['164', 'static/product/ios1_0_864ae5e7f82e1d050cc8aee0b5ff6b01b.png', '1', '6', '2017-09-01 14:03:20', null],
            ['165', 'static/product/ios1_0_8ddef42c34ed791af738b02fbaa98c987.png', '1', '6', '2017-09-01 14:03:20', null],
            /*['289', 'static/avatar/android_3_7_b6de6faf10fb996bf850aa64_4121.jpg', '1', '12', '2017-09-15 18:10:22', null],
            ['290', 'static/avatar/android_3_7_2c12c5f7c375b940fc5d63ff_6436.jpg', '1', '12', '2017-09-15 18:10:22', null],
            ['291', 'static/avatar/android_3_7_ae2f7ac6ac7b6e5d2d3e8130_6146.jpg', '1', '12', '2017-09-15 18:10:22', null],*/
        ];

        $field = ['file_id','file_path','file_type','user_id','created_at','deleted_at'];

        DB::table('system_file')->insert(sql_batch_str($field,$data));
    }
}
