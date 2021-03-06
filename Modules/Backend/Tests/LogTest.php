<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/13
 * Time: 11:00
 */
namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;

class LogTest extends BackendTestCase
{
    public $complete_url = true;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->init();
    }

    public function readyApiParams()
    {
        $params['log_list'] = [
            'method' => 'get',
            'uri' => '/backend/log-list',
            'params' => [

            ],
        ];

        $params['log_detail'] = [
            'method' => 'get',
            'uri' => '/backend/log-detail',
            'params' => [
                'log_id' => 1,
            ],
        ];

        return $params;
    }

    public function testLogList()
    {
        $params = $this->params['log_list'];
        $this->apiTest($params);
    }

    public function testLogDetail()
    {
        $params = $this->params['log_detail'];
        $this->apiTest($params);
    }

}