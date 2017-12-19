<?php

namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RbacTest extends BackendTestCase
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
     * 角色列表
     * @Author gehonghua
     */
    public function testRoleList()
    {
        $this->apiTest($this->params['role_list']);
    }
    /**
     * 所有角色列表
     * @Author gehonghua
     */
    public function testRoleListAll()
    {
        $this->apiTest($this->params['role_list_all']);
    }
    /**
     * 角色详细
     * @Author gehonghua
     */
    public function testRoleDetail()
    {
        $this->apiTest($this->params['role_detail']);
    }
    /**
     * 角色添加
     * @Author gehonghua
     */
    public function testRoleAdd()
    {
        $this->apiTest($this->params['role_add']);
    }
    /**
     * 角色编辑
     * @Author gehonghua
     */
    public function testRoleEdit()
    {
        $this->apiTest($this->params['role_edit']);
    }
    /**
     * 角色删除
     * @Author gehonghua
     */
    public function testRoleDelete()
    {
        $this->apiTest($this->params['role_delete']);
    }
    /**
     * 权限列表
     * @Author gehonghua
     */
    public function testPermissionList()
    {
        $this->apiTest($this->params['permission_list']);
    }
    /**
     * 权限详细
     * @Author gehonghua
     */
    public function testPermissionDetail()
    {
        $this->apiTest($this->params['permission_detail']);
    }
    /**
     * 权限添加
     * @Author gehonghua
     */
    public function testPermissionAdd()
    {
        $this->apiTest($this->params['permission_add']);
    }
    /**
     * 权限编辑
     * @Author gehonghua
     */
    public function testPermissionEdit()
    {
        $this->apiTest($this->params['permission_edit']);
    }
    /**
     * 权限分类
     * @Author gehonghua
     */
    public function testPermissionType()
    {
        $this->apiTest($this->params['permission_type']);
    }
    /**
     * 左侧菜单
     * @Author gehonghua
     */
    public function testPermissionLeft()
    {
        $this->apiTest($this->params['permission_left']);
    }
    /**
     * 角色-权限 添加/编辑
     * @Author gehonghua
     */
    public function testPermissionRoleAdd()
    {
        $this->apiTest($this->params['permission_role_add']);
    }
    /**
     * 角色-权限 详细
     * @Author gehonghua
     */
    public function testPermissionRoleDetail()
    {
        $this->apiTest($this->params['permission_role_detail']);
    }
    /**
     * 测试所用数据
     */
    protected function readyApiParams()
    {
        $params = [];
        #角色列表
        $params['role_list'] = [
            'method' => 'get',
            'uri' => '/backend/role-list',
            'params' => [
            ],
        ];
        #所有角色列表
        $params['role_list_all'] = [
            'method' => 'get',
            'uri' => '/backend/role-list-all',
            'params' => [
            ],
        ];
        #角色详情
        $params['role_detail'] = [
            'method' => 'get',
            'uri' => '/backend/role-detail',
            'params' => [
                'role_id' => 1,
            ],
        ];
        #角色添加
        $params['role_add'] = [
            'method' => 'post',
            'uri' => '/backend/role-add',
            'params' => [
                'name' => 'editor',
                'display_name' => '主编',
                'description' => '网站编辑',
            ],
        ];
        #角色编辑
        $params['role_edit'] = [
            'method' => 'post',
            'uri' => '/backend/role-edit',
            'params' => [
                'role_id' => '2',
                'name' => 'editor',
                'display_name' => '主编2',
                'description' => '网站编辑2',
            ],
        ];
        #角色删除
        $params['role_delete'] = [
            'method' => 'post',
            'uri' => '/backend/role-delete',
            'params' => [
                'role_id' => '2',
            ],
        ];
        #角色-权限 添加/编辑
        $params['permission_role_add'] = [
            'method' => 'post',
            'uri' => '/backend/permission-role-add',
            'params' => [
                'role_id' => '1',
                'permission_id' => '10',
            ],
        ];
        #权限列表
        $params['permission_list'] = [
            'method' => 'get',
            'uri' => '/backend/permission-list',
            'params' => [
            ],
        ];
        #权限详细
        $params['permission_detail'] = [
            'method' => 'get',
            'uri' => '/backend/permission-detail',
            'params' => [
                'permission_id'=>1,
            ],
        ];
        #权限添加
        $params['permission_add'] = [
            'method' => 'post',
            'uri' => '/backend/permission-add',
            'params' => [
                'name' => 'contentManage2',
                'display_name' => '内容管理2',
                'description' => '一级导航2',
                'pid' => '0',
                'level' => '1',
                'path' => '/content2',
                'show' => '1',
            ],
        ];
        #权限编辑
        $params['permission_edit'] = [
            'method' => 'post',
            'uri' => '/backend/permission-edit',
            'params' => [
                'permission_id' => '1',
                'name' => 'contentManage3',
                'display_name' => '内容管理3',
                'description' => '一级导航3',
                'pid' => '0',
                'level' => '1',
                'path' => '/content2',
                'show' => '1',
            ],
        ];
        #权限删除
        $params['permission_delete'] = [
            'method' => 'post',
            'uri' => '/backend/permission-delete',
            'params' => [
                'permission_id' => '3',
            ],
        ];
        #权限类型
        $params['permission_type'] = [
            'method' => 'get',
            'uri' => '/backend/permission-type',
            'params' => [
            ],
        ];
        #左侧菜单
        $params['permission_left'] = [
            'method' => 'get',
            'uri' => '/backend/permission-left',
            'params' => [
            ],
        ];
        #角色-权限 添加/编辑
        $params['permission_role_add'] = [
            'method' => 'post',
            'uri' => '/backend/permission-role-add',
            'params' => [
                'role_id'=>1,
                'permission_id'=>1,
            ],
        ];
        #角色-权限 详细
        $params['permission_role_detail'] = [
            'method' => 'get',
            'uri' => '/backend/permission-role-detail',
            'params' => [
                'role_id'=>1,
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
