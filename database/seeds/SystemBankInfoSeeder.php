<?php

use Illuminate\Database\Seeder;

class SystemBankInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', '1040000', 'bank_logo/1040000.png', '中国银行', '#e25266', '#c7384c', '1041000'],
            ['2', '1030000', 'bank_logo/1030000.png', '农业银行', '#59966e', '#0e7d67', '1031000'],
            ['3', '1020000', 'bank_logo/1020000.png', '工商银行', '#e25266', '#c7384c', '1021000'],
            ['4', '1050000', 'bank_logo/1050000.png', '建设银行', '#4965ac', '#304d7f', '1051000'],
            ['5', '3010000', 'bank_logo/3010000.png', '交通银行', '#4965ac', '#304d7f', '3011000'],
            ['6', '3030000', 'bank_logo/3030000.png', '光大银行', '#dfaf51', '#b89335', '3031000'],
            ['7', '3050000', 'bank_logo/3050000.png', '民生银行', '#59966e', '#0e7d67', '3051000'],
            ['8', '4030000', 'bank_logo/4030000.png', '邮政储蓄', '#59966e', '#0e7d67', '0025840'],
            ['9', '3060000', 'bank_logo/3060000.png', '广发银行', '#e25266', '#c7384c', '3065810'],
            ['10', '3020000', 'bank_logo/3020000.png', '中信银行', '#e25266', '#c7384c', '3021000'],
            ['11', '3040000', 'bank_logo/3040000.png', '华夏银行', '#e25266', '#c7384c', '3041000'],
            ['12', '3100000', 'bank_logo/3100000.png', '浦发银行', '#4965ac', '#304d7f', '3102900'],
            ['13', '3090000', 'bank_logo/3090000.png', '兴业银行', '#4965ac', '#304d7f', '3091000'],
            ['14', '3080000', 'bank_logo/3080000.png', '招商银行', '#e25266', '#c7384c', '3085840'],
        ];

        $field = ['bank_id', 'bank_code', 'bank_logo', 'bank_name', 'color_start', 'color_stop'];

        DB::table('system_bank_info')->insert(sql_batch_str($field, $data));
    }
}
