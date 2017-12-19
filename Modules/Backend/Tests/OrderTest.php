<?php

namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends BackendTestCase
{
    public $params;

    /**
     * 创建测试所用对象
     */
    public function setUp()
    {
        parent::setUp();
        $this->init();
        $this->params = $this->readyApiParams();
    }
//    /**
//     * 订单列表
//     * @Author gehonghua
//     */
//    public function testOrderList()
//    {
//        $this->apiTest($this->params['order_list']);
//    }
    /**
     * 订单详情
     * @Author gehonghua
     */
//    public function testOrderDetail()
//    {
//        $this->apiTest($this->params['order_detail']);
//    }
    /**
     * 订单添加
     * @Author gehonghua
     */
//    public function testOrderAdd()
//    {
//        $this->apiTest($this->params['order_add']);
//    }
    /**
     * 订单编辑
     * @Author gehonghua
     */
//    public function testOrderEdit()
//    {
//        $this->apiTest($this->params['order_edit']);
//    }
    /**
     * 订单拆分
     * @Author gehonghua
     */
    public function testOrderApart()
    {
        $this->apiTest($this->params['order_apart']);
    }
    /**
     * 订单列表
     * @Author CK
     */
    public function testOrderList()
    {
        $this->apiTest($this->params['backendOrder_list']);
    }

    /**
     * 订单详情
     * @Author CK
     */
    public function testOrderDetail()
    {
        $this->apiTest($this->params['backendOrder_detail']);
    }
    /**
     * 测试所用数据
     */
    public function readyApiParams()
    {
        $params = [];
        #订单列表
        $params['order_list'] = [
            'method' => 'get',
            'uri' => '/backend/order-list',
            'params' => [],
        ];
        #订单详细
        $params['order_detail'] = [
            'method' => 'get',
            'uri' => '/backend/order-detail',
            'params' => [
                'order_id'=>1,
            ],
        ];
        #订单添加
        $params['order_add'] = [
            'method' => 'post',
            'uri' => '/backend/order-add',
            'params' => [
                'order_name' => 'order001',
                'order_password' => '111111',
                'order_nick' => '测试昵称',
                'order_sex' => '1',
                'order_birthday' => '1989-03-21',
                'role_id' => '1',
            ],
        ];
        #订单编辑
        $params['order_edit'] = [
            'method' => 'post',
            'uri' => '/backend/order-edit',
            'params' => [
                'order_id' => '1',
                'order_password' => '111111',
                'order_nick' => '测试昵称',
                'order_sex' => '1',
                'order_birthday' => '1989-03-21',
                'role_id' => '1',
            ],
        ];
        #订单删除
        $params['order_apart'] = [
            'method' => 'post',
            'uri' => '/backend/order-apart',
            'params' => [
                'order_sn' => 'O2017091155517',
                'goods_key' => 'G929652281714031'
,               'goods_number' => '3',
                ],
        ];
        #后台订单列表
        $params['backendOrder_list'] = [
            'method' => 'get',
            'uri' => '/backend/order-list',
            'params' => [

            ],
        ];
        #后台订单详情
        $params['backendOrder_detail'] = [
            'method' => 'get',
            'uri' => '/backend/order-detail',
            'params' => [
                'order_id' => 1,

            ],
        ];
        return $params;
    }
    /**
     * 清理测试所用对象
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}
