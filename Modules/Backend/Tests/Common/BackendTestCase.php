<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/13
 * Time: 10:41
 */
namespace Modules\Backend\Tests\Common;

use Tests\Unit\BaseTestCase;

class BackendTestCase extends BaseTestCase
{
    public $complete_url = true;
    #默认header请求头
    protected $default_header = ['Accept' => 'application/vnd.ideabuy.v1+json'];
    #默认user登陆信息
    public $login_params = [
        'backend' => [
            'method' => 'post',
            'uri' => '/backend/admin-login',
            'params' => [
                'admin_name' => 'admin',
                'admin_password' => '111111'
            ],
        ],
    ];
}