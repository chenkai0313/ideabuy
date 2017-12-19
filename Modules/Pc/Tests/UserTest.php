<?php
/**
 * User: caohan
 * Date: 2017/9/13
 * Time: 10:07
 */

namespace Modules\Pc\Tests;

use Modules\Pc\Tests\Common\V1\ApiTestCase;

class UserTest extends ApiTestCase
{
    public $complete_url = true;

    /**
     * ready for data & params
     */
    public function setUp()
    {
        parent::setUp();
        $this->init();

    }

    /**
     * Ready for test params
     */
    protected function readyApiParams()
    {
        $params = [];

        $params['register'] = [
            'method' => 'post',
            'uri' => '/pc/user-register',
            'params' => [
            'user_mobile' => '15757390796',
            'user_password' => 'c12345678',
            'confirm_password' => 'c12345678',
            'code' => '1234']
        ];

        $params['linkman_add'] = [
            'method' => 'post',
            'uri' => '/pc/userlink-add',
            'params' => [
                'link_man' => "曹晗",
                'link_mobile' => "15757390795",
                'link_relation' => "朋友",
            ]
        ];

        $params['userinfo-detail'] = [
            'method' => 'get',
            'uri' => '/pc/userinfo-detail',
            'params' => [],
        ];

        $params['userinfo_add'] = [
            'method' => 'post',
            'uri' => '/pc/userinfo-add',
            'params' => [
            'user_qq' => "404487840",
            'user_portrait'=>"",
            ]
        ];

        $params['changePassword'] = [
            'method' => 'post',
            'uri' => '/pc/user-changepassword',
            'params' => [
                'old_user_password' => 'a1111111',
                'user_password' => 'c12345678',
                'confirm_password' => 'c12345678',
            ]
        ];


        $params['editpaypwd'] = [
            'method' => 'post',
            'uri' => '/pc/user-editpaypwd',
            'params' => [
                'pay_password' => 876556,
                'confirm_pay_pwd'=> 876556,
                'code' => 1111,
            ]
        ];

        $params['validatecreditcode'] = [
            'method' => 'post',
            'uri' => '/api/user-validatecreditcode',
            'params' => [
                'credit_code' => 'PyzahLbx'
            ]
        ];

        $params['userinfo-detail'] = [
            'method' => 'get',
            'uri' => '/api/userinfo-detail',
            'params' => [],
        ];

        $params['user-someinfo']  = [
            'method' => 'get',
            'uri' => '/api/user-realnameidcard',
            'params' => [],
        ];

        $params['payPassword'] = [
            'method' => 'post',
            'uri' => '/pc/user-setpaypwd',
            'params' => [
                'pay_password' => 111111,
                'pay_password_confirm'=>111111
            ]
        ];


        $params['user_black_status'] = [
            'user_id' => '64',
            'status' => 1,
        ];
        $params['user_editidcard'] = [
            'method' => 'post',
            'uri' => '/pc/user-editidcard',
            'params' => [
                'user_idcard' => '330283199508162713',
                'real_name' => '曹晗',
            ]
        ];
        $params['user_editidimg'] = [
            'method' => 'post',
            'uri' => '/pc/user-editidimg',
            'params' => [
                'file' => 'ad/2017/08/25/b2d6ce3422561b3c0ed340031b21dfab.jpg,ad/2017/09/13/c09a121bd4cfd8da7b76d1b06e11867d.jpg,ad/2017/08/18/bc205768dbb85f0912caaec62294ee2a.jpg',
            ]
        ];
        $params['user_bankadd']=[
            'method' => 'post',
            'uri' => '/pc/user-bankadd',
            'params' => [
                'card_number'=>'6222083901004595917',
                'code' => 1111,
                'card_mobile'=>'15757390796',
            ]
        ];
        $params['user_realname']=[
            'method'=>'get',
            'uri'=>'/pc/user-realname',
            'params'=>[]
        ];
        $params['user_banklist']=[
            'method'=>'get',
            'uri'=>'/pc/user-banklist',
            'params'=>[],
        ];
        $params['user_bankdelete']=[
            'method'=>'post',
            'uri'=>'/pc/user-bankdelete',
            'params'=>[
                'card_id'=>1,
            ]
        ];

        $params['getqruuid'] = [
            'method'=>'post',
            'uri' => '/pc/getqruuid',
            'params'=>[
                'url'=>'www.baidu.com',
            ]
        ];

        $params['add-verifycode'] = [
            'method'=>'post',
            'uri' => '/pc/add-verifycode',
            'params' => [
                'user_mobile'=>'15757390796'
            ]
        ];



        return $params;
    }

    protected function readyData()
    {
        //todo logic data
        //\Artisan::call('migrate');

    }

    protected function cleanData()
    {
        //todo logic data
       //\Artisan::call('migrate:reset');
    }


    # 注册
//    public function testUserRegister()
//    {
//        $this->apiTest($this->params['register']);   //验证码存储在redis里
//    }

    # 登录后修改登录密码
    public function testChangePassword() {
        $this->apiTest($this->params['changePassword']);
    }

    # 用户信息获取（完善信息用）
    public function testUserInfoDetail() {
        $this->apiTest($this->params['userinfo-detail']);
    }
    # 联系人添加
    public function testUserLinkAdd() {
        $this->apiTest($this->params['linkman_add']);
    }

    # 用户信息完善
    public function testUserInfoAdd() {
        $this->apiTest($this->params['userinfo_add']);
    }

//    #身份证号码的添加
//    public function testUserEditIdCard()
//    {
//        $this->apiTest($this->params['user_editidcard']);  //身份证被使用
//    }
//    #身份证照片的添加
//    public function testUserEditIdImg()
//    {
//        $this->apiTest($this->params['user_editidimg']);
//    }
//    #银行卡的添加
//    public function testUserBankAdd()
//    {
//        $this->apiTest($this->params['user_bankadd']);
//    }
//    #银行卡添加显示页面
//    public function testUserRealName()
//    {
//        $this->apiTest($this->params['user_realname']);
//    }
//    #银行卡列表
//    public function testUserBankList()
//    {
//        $this->apiTest($this->params['user_banklist']);
//    }
//    #银行卡的删除
//    public function testUserBankDelete()
//    {
//        $this->apiTest($this->params['user_bankdelete']);
//    }
    #设置交易密码
    public function testUserSetPayPwd()
    {
        $this->apiTest($this->params['payPassword']);
    }
    #重置交易密码
    public function testUserEditPayPwd()
    {
        $this->apiTest($this->params['editpaypwd']);
    }

    #Pc端获取qruuid
    public function testGetqruuid() {
        $this->apiTest($this->params['getqruuid']);
    }

    #Pc端获取验证码
    public function testAddverifycode() {
        $this->apiTest($this->params['add-verifycode']);
    }

    /**
     * Drop something test data
     */
    public function tearDown() {
        //$this->cleanData();
        parent::tearDown();
    }

}