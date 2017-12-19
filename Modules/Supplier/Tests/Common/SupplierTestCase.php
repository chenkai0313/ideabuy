<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/10/9
 * Time: 9:34
 */

namespace Modules\Supplier\Tests\Common;


use Tests\Unit\BaseTestCase;

class SupplierTestCase extends BaseTestCase
{
    public $complete_url = true;
    #默认header请求头
    protected $default_header = ['Accept' => 'application/vnd.ideabuy.v1+json'];
    #默认user登陆信息
    public $login_params = [
        'supplier' => [
            'method' => 'post',
            'uri' => '/supplier/supplier-login',
            'params' => [
                'supplier_mobile' => '17611110000',
                'supplier_password' => '111111'
            ],
        ],
    ];
}