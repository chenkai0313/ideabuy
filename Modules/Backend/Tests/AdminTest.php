<?php

namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends BackendTestCase
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
    /**
     * 管理员列表
     * @Author gehonghua
     */
    public function testAdminList()
    {
        $this->apiTest($this->params['admin_list']);
    }
    /**
     * 管理员详情
     * @Author gehonghua
     */
    public function testAdminDetail()
    {
        $this->apiTest($this->params['admin_detail']);
    }
    /**
     * 管理员添加
     * @Author gehonghua
     */
    public function testAdminAdd()
    {
        $this->apiTest($this->params['admin_add']);
    }
    /**
     * 管理员编辑
     * @Author gehonghua
     */
    public function testAdminEdit()
    {
        $this->apiTest($this->params['admin_edit']);
    }
    /**
     * 管理员删除
     * @Author gehonghua
     */
    public function testAdminDelete()
    {
        $this->apiTest($this->params['admin_delete']);
    }
    /**
     * 测试所用数据
     */
    public function readyApiParams()
    {
        $params = [];
        #管理员列表
        $params['admin_list'] = [
            'method' => 'get',
            'uri' => '/backend/admin-list',
            'params' => [],
        ];
        #管理员详细
        $params['admin_detail'] = [
            'method' => 'get',
            'uri' => '/backend/admin-detail',
            'params' => [
                'admin_id'=>1,
            ],
        ];
        #管理员添加
        $params['admin_add'] = [
            'method' => 'post',
            'uri' => '/backend/admin-add',
            'params' => [
                'admin_name' => 'admin001',
                'admin_password' => '111111',
                'admin_nick' => '测试昵称',
                'admin_sex' => '1',
                'admin_birthday' => '1989-03-21',
                'role_id' => '1',
            ],
        ];
        #管理员编辑
        $params['admin_edit'] = [
            'method' => 'post',
            'uri' => '/backend/admin-edit',
            'params' => [
                'admin_id' => '1',
                'admin_password' => '111111',
                'admin_nick' => '测试昵称',
                'admin_sex' => '1',
                'admin_birthday' => '1989-03-21',
                'role_id' => '1'
            ],
        ];
        #管理员删除
        $params['admin_delete'] = [
            'method' => 'post',
            'uri' => '/backend/admin-delete',
            'params' => [
                'admin_id' => '2',
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
