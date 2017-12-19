<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/10/9
 * Time: 14:50
 */

namespace Modules\Supplier\Tests;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


use Modules\Supplier\Tests\Common\SupplierTestCase;

class SupplierTest extends SupplierTestCase
{
    public $params;
//    use WithoutMiddleware;
    /**
     * 创建测试所用对象
     */
    public function setUp()
    {
        parent::setUp();
        $this->init();
    }
    public function readyApiParams()
    {
        #供应商添加
        $params['supplier_add'] = [
            'method' => 'post',
            'uri' => '/supplier/supplier-add',
            'params' => [
                'supplier_mobile' => '18077774444',
                'supplier_password' => '111111',
                'province' => '33',
                'city' => '3302',
                'district' => '330283',
                'address' => 'XX街道旁',
                'supplier_name' => 'TEST供应商',
            ],
        ];
        $params['supplier_list'] = [
            'method' => 'get',
            'uri' => '/supplier/supplier-list',
            'params' => [
                'keyword' => '180',
            ],
        ];
        $params['supplier_edit'] = [
            'method' => 'post',
            'uri' => '/supplier/supplier-edit',
            'params' => [
                'supplier_id' => '2',
                'supplier_mobile' => '18077774444',
                'supplier_password' => '111111',
                'province' => '33',
                'city' => '3302',
                'district' => '330283',
                'address' => 'XX街道旁',
                'supplier_name' => 'TEST供应商',
                'remark' => 'TEST供应商',
            ],
        ];
        $params['supplier_delete'] = [
        'method' => 'post',
        'uri' => '/supplier/supplier-delete',
        'params' => [
            'supplier_id' => '2',
        ],
    ];
        $params['supplier_detail'] = [
            'method' => 'post',
            'uri' => '/supplier/supplier-detail',
            'params' => [
                'supplier_id' => '2',
            ],
        ];

        return $params;
    }
    /**
     * 添加供应商
     */
    public function testSupplierAdd()
    {
        $this->apiTest($this->params['supplier_add']);
    }
    /**
     * 供应商编辑
     */
    public function testSupplierEdit()
    {
        $this->apiTest($this->params['supplier_edit']);
    }
    /**
     * 供应商详情
     */
    public function testSupplierDetail()
    {
        $this->apiTest($this->params['supplier_detail']);
    }
    /**
     * 供应商列表
     */
    public function testSupplierList()
    {
        $this->apiTest($this->params['supplier_list']);
    }
    /**
     * 删除供应商
     */
    public function testSupplierDelete()
    {
        $this->apiTest($this->params['supplier_delete']);
    }
}