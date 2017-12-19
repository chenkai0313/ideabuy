<?php

namespace Modules\Api\Tests;

use Modules\Api\Tests\Common\ApiTestCase;


/**
 * Author DongMing.Cui
 * Describe This is account test case demo
 * Class AccountTest
 * @package Modules\Api\Tests
 */

class AccountTest extends ApiTestCase
{
    public $user;
    public $authHeader;
    public $params;
    public $data;

    /**
     * ready for data & params
     */
    public function setUp()
    {
        parent::setUp();

        $this->params = $this->readyApiParams();
        $this->data = $this->readyData();

        /** Get auth info include token  */
        $this->user = $this->userLogin($this->params['login']);

        $this->authHeader = ['Authorization' => 'Bearer '.$this->user['data']['token']];

    }

    /**
     * Ready for test params
     */
    protected function readyApiParams()
    {
        $params = [];

        /** login params for test **/

        $login = [
            'user_mobile' => '15105840179',
            'user_password' => 'a1111111'
        ];
        $params['login'] = $login;

        /** register user params for test **/

        $register = [
            'user_mobile' => '15105840179',
            'user_password' => 'a1111111',
            'confirm_password' => 'a1111111',
            'code' => '1234'
        ];
        $params['register'] = $register;

        /** setting user paypassword params for test **/

        $payPassword = ['pay_password' => '111111'];
        $params['payPassword'] = $payPassword;

        return $params;
    }

    /**
     * Ready for test data
     */
    protected function readyData()
    {
        //todo logic data
        //Artisan::call('migrate');

    }

    /**
     * Clean  test data
     */
    protected function cleanData()
    {
        //todo logic data
        //Artisan::call('migrate:reset');
    }

    /**
     * this is case for user login
     */
    public function testUserLogin()
    {
        $this->assertEquals(1, $this->user['code']);
    }

    /**
     * This is case for user register
     */
    public function testUserRegister()
    {
        $response = $this->post(
            '/api/user-register',
            $this->params['register'],
            $this->initHeader()
        );

        $result = $response->getOriginalContent();

        $this->assertEquals(10133, $result['code']);//短信验证码不存在  (短信发不出 因为被注册了)
    }

    public function testIdeabuyIndex() {
        $response = $this->post(
            '/api/ideabuy-index',
            [],
            $this->initHeader()
        );
        $result = $response->getOriginalContent();
        $this->assertEquals(1, $result['code']);
    }
    /**
     * This is case for setting pay password
     */
    public function testSetPayPassword()
    {
        $response = $this->post(
            '/api/user-setpaypwd',
                $this->params['payPassword'],
                $this->initHeader($this->authHeader)
        );
        dd($this->initHeader($this->authHeader));
        $result = $response->getOriginalContent();
        dd($result);
        $this->assertEquals(1, $result['code']);
    }


    /**
     * Drop something test data
     */
    public function tearDown()
    {
        $this->cleanData();

        parent::tearDown();

    }

}