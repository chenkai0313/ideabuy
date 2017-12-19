<?php

namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;

class MsgtemplateTest extends BackendTestCase
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
     * 消息模版添加
     * @Author CK
     */
    public function testMsgtemplateAdd()
    {
        $this->apiTest($this->params['msgtemplate_add']);
    }

    /**
     * 消息模版详情
     * @Author CK
     */
    public function testMsgtemplateDetail()
    {
        $this->apiTest($this->params['msgtemplate_detail']);
    }

    /**
     * 消息模版列表
     * @Author CK
     */
    public function testMsgtemplateList()
    {
        $this->apiTest($this->params['msgtemplate_list']);
    }


    /**
     * 消息模版修改
     * @Author CK
     */
    public function testMsgtemplateEdit()
    {
        $this->apiTest($this->params['msgtemplate_edit']);
    }


    /**
     * 消息模版删除
     * @Author CK
     */
    public function testMsgtemplateDelete()
    {
        $this->apiTest($this->params['msgtemplate_delete']);
    }

    /**
     * 消息模版关键字的添加
     * @Author CK
     */
    public function testMsgtemplateKeywordAdd()
    {
        $this->apiTest($this->params['msgtemplatekeyword_add']);
    }

    /**
     * 消息模版关键字的列表
     * @Author CK
     */
    public function testMsgtemplateKeywordList()
    {
        $this->apiTest($this->params['msgtemplatekeyword_list']);
    }

    /**
     * 消息模版关键字的修改
     * @Author CK
     */
    public function testMsgtemplateKeywordEdit()
    {
        $this->apiTest($this->params['msgtemplatekeyword_edit']);
    }

    /**
     * 消息模版关键字的详情
     * @Author CK
     */
    public function testMsgtemplateKeywordDetail()
    {
        $this->apiTest($this->params['msgtemplatekeyword_detail']);
    }


    /**
     * 测试所用数据
     */
    public function readyApiParams()
    {
        $params['msgtemplate_detail'] = [
            'method' => 'get',
            'uri' => '/backend/msgtemplate-detail',
            'params' => [
                'id' => 1,

            ],
        ];
        $params['msgtemplate_list'] = [
            'method' => 'get',
            'uri' => '/backend/msgtemplate-list',
            'params' => [

            ],
        ];
        $params['msgtemplate_add'] = [
            'method' => 'post',
            'uri' => '/backend/msgtemplate-add',
            'params' => [
                'content' => '123',
                'prepare_node' => '123',
                'msg_type' => '123',
                'msg_tag' => '123',
                'msg_title' => '123',
            ],
        ];
        $params['msgtemplate_delete'] = [
            'method' => 'post',
            'uri' => '/backend/msgtemplate-delete',
            'params' => [
                'id' => 1,
            ],
        ];
        $params['msgtemplate_edit'] = [
            'method' => 'post',
            'uri' => '/backend/msgtemplate-edit',
            'params' => [
                'id' => 1,
                'content' => '123',
                'prepare_node' => '123',
                'msg_type' => '123',
                'msg_tag' => '123',
                'msg_title' => '123',
            ],
        ];
        $params['msgtemplatekeyword_add'] = [
            'method' => 'post',
            'uri' => '/backend/msgtemplatekeyword-add',
            'params' => [
                'keyword_name' => '${123}',
                'keyword_zh' => '123',
            ],
        ];
        $params['msgtemplatekeyword_list'] = [
            'method' => 'get',
            'uri' => '/backend/msgtemplatekeyword-list',
            'params' => [

            ],
        ];
        $params['msgtemplatekeyword_edit'] = [
            'method' => 'post',
            'uri' => '/backend/msgtemplatekeyword-edit',
            'params' => [
                'keyword_id' => 1,
                'keyword_name' => '${123}',
                'keyword_zh' => '123',
            ],
        ];
        $params['msgtemplatekeyword_detail'] = [
            'method' => 'get',
            'uri' => '/backend/msgtemplatekeyword-detail',
            'params' => [
                'keyword_id' => 1,
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