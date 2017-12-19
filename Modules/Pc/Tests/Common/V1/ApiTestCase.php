<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/13
 * Time: 10:41
 */
namespace Modules\Pc\Tests\Common\V1;

use Tests\Unit\BaseTestCase;

class ApiTestCase extends BaseTestCase
{
    public $complete_url = true;

    #默认header请求头
    protected $default_header = ['Accept' => 'application/vnd.ideabuy.v1+json'];

    #默认user登陆信息
    public $login_params = [
        'api' => [
            'method' => 'post',
            'uri' => '/pc/user-login',
            'params' => [
                'user_mobile' => '15105840179',
                'user_password' => 'a1111111'
            ],
        ]
    ];
}