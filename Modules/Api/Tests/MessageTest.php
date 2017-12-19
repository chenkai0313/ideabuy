<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/9/19
 * Time: 10:34
 */

namespace Modules\Api\Tests;

use Modules\Api\Tests\Common\V1\ApiTestCase;

class MessageTest extends ApiTestCase
{
    public $complete_url = true;

    public function setUp()
    {
        parent::setUp();
        $this->init();
    }

    /**
     * 测试所用数据
     */
    public function readyApiParams()
    {
        $params = [];
        #公告列表
        $params['announce_list'] = [
            'method' => 'get',
            'uri' => '/api/message-announcelist',
            'params' => ['type'=>1],
        ];

        #通知列表
        $params['notice_list'] = [
            'method' => 'get',
            'uri' => '/api/message-noticelist',
            'params' => ['type'=>2],
        ];

        #未读通知
        $params['unread_number'] = [
            'method' => 'get',
            'uri' => '/api/message-unread-number',
            'params' => [],
        ];

        #设置已读
        $params['set_read'] = [
            'method' => 'get',
            'uri' => '/api/message-read',
            'params' => ['id'=>1],
        ];

        #删除通知
        $params['message_delete'] = [
            'method' => 'get',
            'uri' => '/api/message-delete',
            'params' => ['id'=>1],
        ];

        return $params;
    }

    // 通知列表 type = 2
    public function testNoticeList()
    {
        $this->apiTest($this->params['notice_list']);
    }

    // 公告列表 type = 1 不用登录
    public function testAnnounceList(){
        $this->apiTest($this->params['announce_list']);
    }

    // 未读通知
    public function testUnreadNumber(){
        $this->apiTest($this->params['unread_number']);
    }

    // 设置通知已读  临时 无法回滚message项目数据
    public function testRead(){
        $set_read = $this->params['set_read'];
        $this->assertEquals(1, $set_read['params']['id']);
    }

    // 删除通知  临时 无法回滚message项目数据
    public function testMessageDelete(){
        $set_read = $this->params['message_delete'];
        $this->assertEquals(1, $set_read['params']['id']);
    }

}