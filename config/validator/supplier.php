<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/26
 * Time: 17:05
 */
return [
    #供应商
    'supplier' => [
        #供应商添加
        'supplier-add' => [
            'supplier_mobile' => array('regex:/^1[34578]+\d{9}$/', 'required'),
            'supplier_name' => array('max:20','required'),
            'supplier_password' => 'required',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required'
        ],
        'supplier-val' => [
            'supplier_mobile' => '供应商手机号',
            'supplier_password' => '供应商密码',
            'confirm_password' => '确认密码',
            'province' => '省份',
            'city' => '市',
            'district' => '区',
            'address' => '详细地址',
        ],
        'supplier-key' => [
            'required' => ':attribute必填',
            'regex' => ':attribute格式不正确',
            'max' => ':attribute太长',
        ],
        'supplier-edit' => [
            'supplier_mobile' => array('regex:/^1[34578]+\d{9}$/', 'required'),
            'supplier_name' => array('max:20','required'),
            'supplier_password' => 'required',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required'
        ],
        'supplier-login' => [
            'supplier_mobile' => 'required',
            'supplier_password' => 'required',
        ],
    ],
];