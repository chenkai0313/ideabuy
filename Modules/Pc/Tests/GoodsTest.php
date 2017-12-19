<?php
/**
 * User: caohan
 * Date: 2017/9/13
 * Time: 10:07
 */

namespace Modules\Pc\Tests;

use Modules\Pc\Tests\Common\V1\ApiTestCase;

class GoodsTest extends ApiTestCase
{

    /**
     * ready for data & params
     */
    public function setUp()
    {
        parent::setUp();
        $this->init();
    }

    /**
     * Ready for test params
     */
    protected function readyApiParams()
    {
        $params = [];

        $params['goods_list'] = [
            'method' => 'get',
            'uri' => '/pc/goods-list',
            'params' => [

            ],
        ];

        $params['goods_detail'] = [
            'method' => 'get',
            'uri' => '/pc/goods-detail',
            'params' => [
                'goods_id' => 1,
            ],
        ];

        $params['cart_add'] = [
        'method' => 'post',
        'uri' => '/pc/cart-add',
        'params' => [
            'admin_id' => '1',
            'goods_id' => '2',
            'product_id' => '1',
            'goods_sn' => '1231234',
            'goods_name' => 'goodsname',
            'goods_number' => '1',
            'goods_attr' => '1',
            'market_price' => '10',
            'product_price' => '9',
            'goods_thumb' => '123',
            'goods_img' => '456',
            ],
        ];

        $params['cart_list'] = [
            'method' => 'post',
            'uri' => '/pc/cart-list',
            'params' => [ ],
        ];

        $params['cart_del'] = [
            'method' => 'post',
            'uri' => '/pc/cart-del',
            'params' => [
                'cart_id' => 1,
            ],
        ];


        return $params;
    }

    public function testGoodsList() {
        $this->apiTest($this->params['goods_list']);
    }

    public function testGoodsDetail() {
        $this->apiTest($this->params['goods_detail']);
    }

    public function testCartAdd() {
        $this->apiTest($this->params['cart_add']);
    }

    public function testCartList() {
        $this->apiTest($this->params['cart_list']);
    }

    public function testCartDel() {
        $this->apiTest($this->params['cart_del']);
    }
}