<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/9/13
 * Time: 14:43
 */

namespace Modules\Api\Tests;

use Modules\Api\Tests\Common\V1\ApiTestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;


class UserOrderTest extends ApiTestCase
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


    protected function readyApiParams()
    {
        $params = [];

        #分期明细(该分期的大概信息+所有信息)
        $params['instalmentinfo'] = [
            'method' => 'get',
            'uri' => '/api/user-instalmentinfo',
            'params' => [
                "contract_sn"=>"C2017081526800",
            ],
        ];

        $params['repaymentsindex'] = [
            'method' => 'get',
            'uri' => '/api/user-repaymentsindex',
            'params' => [],

        ];

        $params['getinstalltypeplan'] = [
            'method' => 'post',
            'uri' => '/api/user-getinstalltypeplan',
            'params' => ['amount'=>5000],
        ];

        $params['overdueinfo'] = [
            'method' => 'get',
            'uri' => '/api/user-overdueinfo',
            'params' => [
                'date' => '2017-08',
                ],
        ];

        $params['Allbill'] = [
            'method' => 'get',
            'uri' => '/api/user-allbill',
            'params' => [],
        ];

        $params['ImmediateRepayment'] = [
            'method' => 'post',
            'uri' => '/api/user-immediate-repayment',
            'params' => [
                'date' => date('Y-h'),
            ],
        ];

        $params['ConfirmInstall'] = [
            'method' => 'post',
            'uri' => '/api/user-confirm-install',
            'params' => [
                'order_sn' => '123456',
                'month' => 6,
                'amount' => 6,
            ],
            'code'=>'500'  //订单不存在，分期失败
        ];

        return $params;
    }

    protected function readyData() {
        //todo logic data
        //\Artisan::call('migrate');
    }

    protected function cleanData() {
        //todo logic data
        //\Artisan::call('migrate:reset');
    }

    public function tearDown() {
        //$this->cleanData();
        parent::tearDown();
    }

    #分期明细(该分期的大概信息+所有信息)
    public function testInstalmentinfo() {
       $this->apiTest($this->params['instalmentinfo']);
    }

    #我的账单(账单首页)
    public function testRepaymentsindex() {
        $this->apiTest($this->params['repaymentsindex']);
    }
    #各个分期方法查询 通过amount 获取3期 6期.....
    public function testGetinstalltypeplan() {
        $this->apiTest($this->params['getinstalltypeplan']);
    }

    #逾期明细
    public function testOverdueinfo() {
        $this->apiTest($this->params['overdueinfo']);
    }

    #全部账单
    public function testAllbill() {
        $this->apiTest($this->params['Allbill']);
    }

    #立即还款按钮
    public function testImmediateRepayment() {
        $this->apiTest($this->params['ImmediateRepayment']);
    }

    #确认分期按钮
    public function testConfirmInstall() {
       $this->apiTest($this->params['ConfirmInstall']);
    }


}