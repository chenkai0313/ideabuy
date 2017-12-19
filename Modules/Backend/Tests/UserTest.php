<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/13
 * Time: 11:00
 */
namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;

class UserTest extends BackendTestCase
{
    public $params;

    /**
     * 创建测试所用对象
     */
    protected function setUp()
    {
        parent::setUp();
        $this->init();
        $this->params = $this->readyApiParams();
    }

    /**
     * 测试所用数据
     */
    public function readyApiParams()
    {

        #用户列表
        $params['user_list'] = [
            'method' => 'get',
            'uri' => '/backend/user-list',
            'params' => [

            ],
        ];
        #用户详情
        $params['user_detail'] = [
            'method' => 'get',
            'uri' => '/backend/user-detail',
            'params' => [
                'user_id' => 2,
            ],
        ];
        #审核详情
        $params['userapply_detail'] = [
            'method' => 'get',
            'uri' => '/backend/userapply-detail',
            'params' => [
                'user_id' => 1,
            ],
        ];
        #审核列表
        $params['userapply_list'] = [
            'method' => 'get',
            'uri' => '/backend/userapply-list',
            'params' => [
            ],
        ];
        #审核操作
        $params['userapply_edit'] = [
            'method' => 'post',
            'uri' => '/backend/userapply-edit',
            'params' => [
                'user_id' => 1,
                'status' => 1,
                'reason' => '帅吧'
            ],
            //'code'=>10072//审核已通过
        ];
        #用户添加
        $params['user_add'] = [
            'method' => 'post',
            'uri' => '/backend/user-add',
            'params' => [
                'user_mobile' => 15381812888,
                'user_password' => 'ck123123123',
                'user_idcard' => '340821199503132713',
                'real_name' => '测试',
            ],
        ];

        return $params;
    }

    /**
     * 用户添加
     * @Author CK
     */
    public function testUserAdd()
    {
        $this->apiTest($this->params['user_add']);
    }


    /**
     * 用户列表
     * @Author CK
     */
    public function testUserList()
    {
        $this->apiTest($this->params['user_list']);
    }

    /**
     * 用户详情
     * @Author CK
     */
    public function testUserDetail()
    {
        $this->apiTest($this->params['user_detail']);
    }

    /**
     * 审核详情
     * @Author CK
     */
    public function testUserApplyDetail()
    {
        $this->apiTest($this->params['userapply_detail']);
    }

    /**
     * 审核列表
     * @Author CK
     */
    public function testUserApplyList()
    {
        $this->apiTest($this->params['userapply_list']);
    }

    /**
     * 审核操作
     * @Author CK
     */
    public function testUserApplyEdit()
    {
        $this->apiTest($this->params['userapply_edit']);
    }

}