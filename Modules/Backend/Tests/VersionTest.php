<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/10/9
 * Time: 10:02
 */
namespace Modules\Backend\Tests;

use Modules\Backend\Tests\Common\BackendTestCase;

class VersionTest extends BackendTestCase
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
        #版本新增
        $params['version-add']=[
            'method' => 'post',
            'uri' => '/backend/version-add',
            'params' => [
               'json'=>'{"json":true,"version":"1.2.3","device":"ios","update_type":"1","update_mode":"1","version_content":"123","module":"1","version_url":[{"file_path":"ios/atom.exe","file_md5":"a0ac797f1ca58ec9a3848e5fe8eb58c7"},{"file_path":"ios/bg1.jpg","file_md5":"79abedd3fe4528eb72b31c9a0d9f3d8e"}]}'
            ],
        ];
        #版本新增显示
        $params['version-dispaly']=[
            'method' => 'get',
            'uri' => '/backend/version-dispaly',
            'params' => [
            ],
        ];
        #版本新增显示
        $params['version-list']=[
            'method' => 'get',
            'uri' => '/backend/version-list',
            'params' => [
            ],
        ];
        #版本删除
        $params['version-delete']=[
            'method' => 'post',
            'uri' => '/backend/version-delete',
            'params' => [
                'id'=>'1,2'
            ],
        ];
        return $params;
    }
    #版本新增
    public function testVersionAdd()
    {
        $this->apiTest($this->params['version-add']);
    }
    #版本新增显示
    public function testVersionAddDisplay()
    {
        $this->apiTest($this->params['version-dispaly']);
    }
    #版本列表
    public function testVersionList()
    {
        $this->apiTest($this->params['version-list']);
    }
    #版本删除
    public function testVersionDelete()
    {
        $this->apiTest($this->params['version-delete']);
    }
}