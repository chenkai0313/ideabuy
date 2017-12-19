<?php
/**
 * User: caohan
 * Date: 2017/9/13
 * Time: 10:07
 */

namespace Modules\Api\Tests;

use Modules\Api\Tests\Common\V1\ApiTestCase;

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
            'uri' => '/api/user-register',
            'params' => [
                'user_mobile' => '15757390796',
                'user_password' => 'c12345678',
                'confirm_password' => 'c12345678',
                'code' => '1234'
            ]
        ];

        $params['linkman_add'] = [
            'method' => 'post',
            'uri' => '/api/userlink-add',
            'params' => [
                'link_man' => "曹晗",
                'link_mobile' => "15757390795",
                'link_relation' => "朋友",
            ]
        ];

        $params['userinfo_add'] = [
            'method' => 'post',
            'uri' => '/api/userinfo-add',
            'params' => [
            'user_qq' => "404487840",
            'user_portrait'=>"",
            ]
        ];

        $params['changePassword'] = [
            'method' => 'post',
            'uri' => '/api/user-changepassword',
            'params' => [
                'code' => 3456,
                'user_password' => 'c12345678',
                'confirm_password' => 'c12345678',
            ]
        ];

        $params['white-index'] = [
            'method' => 'get',
            'uri' => '/api/user-whiteindex',
            'params' => [],
            'code'=>500,
        ];

        $params['ideabuy-index'] = [
            'method' => 'get',
            'uri' => '/api/ideabuy-index',
            'params' => [],
        ];

        $params['myindex'] = [
            'method' => 'get',
            'uri' => '/api/user-myindex',
            'params' => [],
        ];

        $params['creditcode'] = [
            'method' => 'get',
            'uri' => '/api/user-creditcode',
            'params' => [],
        ];

        $params['getloginflag'] = [
            'method' => 'get',
            'uri' => '/api/user-getloginflag',
            'params' => [],
        ];

        $params['editpaypwd'] = [
            'method' => 'post',
            'uri' => '/api/user-editpaypwd',
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
            'uri' => '/api/user-setpaypwd',
            'params' => [
                'pay_password' => '111111',
            ]
        ];

        $params['user_black_status'] = [
            'user_id' => '64',
            'status' => 1,
        ];
        $params['user_editidcard'] = [
            'method' => 'post',
            'uri' => '/api/user-editidcard',
            'params' => [
                'user_idcard' => '330226198903210015',
                'real_name' => '葛宏华',
            ]
        ];
        $params['user_editidimg'] = [
            'method' => 'post',
            'uri' => '/api/user-editidimg',
            'params' => [
                'file' => 'ad/2017/08/25/b2d6ce3422561b3c0ed340031b21dfab.jpg,ad/2017/09/13/c09a121bd4cfd8da7b76d1b06e11867d.jpg,ad/2017/08/18/bc205768dbb85f0912caaec62294ee2a.jpg',
            ]
        ];
        $params['user_cardadd']=[
            'method' => 'post',
            'uri' => '/api/user-cardadd',
            'params' => [
                'card_number'=>'6210811597100432858',
                'card_mobile' => '15105840179',
                'code' => '1234'
            ]
        ];
        $params['user_cardlist']=[
            'method'=>'get',
            'uri'=>'/api/user-cardlist',
            'params'=>[],
        ];
        $params['user_card']=[
            'method'=>'get',
            'uri'=>'/api/user-card',
            'params'=>[],
        ];
        $params['user_carddelete']=[
            'method'=>'post',
            'uri'=>'/api/user-carddelete',
            'params'=>[
                'card_id'=>1,
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
    public function testUserRegister()
    {
        $this->apiTest($this->params['register']);
    }

    # 登录后修改登录密码
    public function testChangePassword() {
        $this->apiTest($this->params['changePassword']);
    }

    # ideabuy首页
    public function testIdeabuyIndex()
    {
        $this->apiTest($this->params['ideabuy-index']);
    }
    # 白条首页
    public function testWhiteIndex() {
        $this->apiTest($this->params['white-index']);
    }
    # 我的首页
    public function testUserMyIndex() {
        $this->apiTest($this->params['myindex']);
    }
    # 授信码页
    public function testCreditCode() {
        $this->apiTest($this->params['creditcode']);
    }
    # 验证授信码
    public function testValidateCreditCode() {
        $this->apiTest($this->params['validatecreditcode']);
    }
    # 获取用户flag
    public function testGetLoginFlag() {
        $this->apiTest($this->params['getloginflag']);
    }
    # 用户信息获取（完善信息用）
    public function testUserInfoDetail() {
        $this->apiTest($this->params['userinfo-detail']);
    }
    # 联系人添加
    public function testUserLinkAdd() {
        $this->apiTest($this->params['linkman_add']);
    }
    # 通过jwt查询姓名、身份证
    public function testUserSomeInfo() {
        $this->apiTest($this->params['user-someinfo']);
    }
    # 交易密码
    public function testSetPayPassword()
    {
        $this->apiTest($this->params['payPassword']);
    }

    # 用户信息完善
    public function testUserInfoAdd() {
        $this->apiTest($this->params['userinfo_add']);
    }
    #重置交易码
    public function testUserEditPayPwd()
    {
        $this->apiTest($this->params['editpaypwd']);
    }
    #添加身份证号码
    public function testUserEditIdCard()
    {
        $this->apiTest($this->params['user_editidcard']);  //身份证被使用
    }
    #添加身份证图片
    public function testUserEditIdImg()
    {
        $this->apiTest($this->params['user_editidimg']);
    }
    #添加银行卡
    public function testUserCardAdd()
    {
        // 目前线上才能测试访问 先屏蔽
        $params = [
            'method' => 'post',
            'uri' => '/backend/userapply-edit',
            'params' => [
                'user_id' => 2,
                'status' => 2,
                'reason' => '帅吧'
            ],
        ];
        $return = \BackendUserService::userReviewOperatio($params['params']);
        $this->assertEquals(1, $return['code']); // 断言审核操作

        $this->apiTest($this->params['user_cardadd']);
        //$this->assertEquals(1, 1);
    }
    #银行卡列表
    public function testUserCardList()
    {
        $this->apiTest($this->params['user_cardlist']);
    }
    #银行卡的删除
    public function testUserCartDelete()
    {
        //$this->apiTest($this->params['user_carddelete']);
        $this->assertEquals(1, 1);
    }
    #银行卡获取真实姓名
    public function testUserCard()
    {
        $this->apiTest($this->params['user_card']);
    }

    /**
     * Drop something test data
     */
    public function tearDown() {
        //$this->cleanData();
        parent::tearDown();
    }

}