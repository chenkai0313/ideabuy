<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/9/20
 * Time: 9:47
 */

namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;

class MessageTest extends BackendTestCase
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
     * 测试所用数据
     */
    public function readyApiParams()
    {
        $params = [];

        #推送列表
        $params['push_list'] = [
            'method' => 'get',
            'uri' => '/backend/push-list',
            'params' => [
                'keyword' => '通知'
            ],
        ];

        #消息列表-公告
        $params['announce_list'] = [
            'method' => 'get',
            'uri' => '/backend/message-announcelist',
            'params' => [
                // 'keyword' => '通知'
            ],
        ];

        #消息列表-通知
        $params['notice_list'] = [
            'method' => 'get',
            'uri' => '/backend/message-noticelist',
            'params' => [
                // 'keyword' => '通知'
            ],
        ];

        #短信列表
        $params['sms_list'] = [
            'method' => 'get',
            'uri' => '/backend/message-smslist',
            'params' => [
                'keyword'=>'15988346742'
            ],
        ];

        #公告置顶
        $params['message_announcetop'] = [
            'method' => 'post',
            'uri' => '/backend/message-announcetop',
            'params' => [
                'id' => '3'
            ],
        ];

        #推送参数获取
        $params['push_select'] = [
            'method' => 'get',
            'uri' => '/backend/message-push-select',
            'params' => [],
        ];

        #推送
        $params['push_data'] = [
            'method' => 'post',
            'uri' => '/backend/message-push',
            'params' => [
                'type' => 2,                    // 主消息类型 1公告 2通知
                'message_type' => 'repayment_reminder',     // 子消息类型 0 message_announcement 群发公告 1 user_apply 审核 身份认证 2 active_white 激活白条 3 order_status 订单状态 4 repayment_reminder 还款提醒 5 collection_reminder 催收提醒
                'title' => '测试标题-'.time(),  // 标题
                'description' => '测试内容-测试、测试、测试',  // 内容描述
                'audience' => 'regis_id',       // 发送对象 所有人all 个人regis_id
                'operate_type' => 3,            // 操作类型 1仅推送 2短消息 3推送and消息
                'user_id' => 1,                 // user_id = 1 为测试主账号
            ],
        ];

        return $params;
    }

    /**
     * 推送列表
     * @Author yefan
     */
    public function testPushList()
    {
        $this->apiTest($this->params['push_list']);
    }

    // 消息列表-公告
    public function testAnnounceList()
    {
        $this->apiTest($this->params['announce_list']);
    }

    // 消息列表-通知
    public function testNoticeList()
    {
        $this->apiTest($this->params['notice_list']);
    }

    // 短息列表
    public function testSmsList()
    {
        $this->apiTest($this->params['sms_list']);
    }

    // 公告置顶  临时 无法回滚message项目数据
    public function testMessageTop(){
        $set_read = $this->params['message_announcetop'];
        $this->assertEquals(3, $set_read['params']['id']);
    }

    // 推送参数选择
    public function testPushSelect(){
        $this->apiTest($this->params['push_select']);
    }

    // 推送操作
    public function testPushOperate(){
        $this->apiTest($this->params['push_data']);
    }

    /**
     * 清理测试所用对象
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}
