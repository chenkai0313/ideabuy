<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/27
 * Time: 17:05
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',function ($api) {
    #无需身份验证
    $api->group(['namespace' => 'Modules\Supplier\Http\Controllers','prefix' => 'supplier'], function ($api) {

    });
    #需要身份验证
    $api->group(['namespace' => 'Modules\Supplier\Http\Controllers','prefix' => 'supplier','middleware'=>['jwt-supplier']], function ($api) {
        #供应商curd
        $api->post('supplier-login', 'SupplierController@supplierLogin');
        $api->post('supplier-add', 'SupplierController@supplierAdd');
        $api->get('supplier-list', 'SupplierController@supplierList');
        $api->post('supplier-edit', 'SupplierController@supplierEdit');
        $api->post('supplier-delete', 'SupplierController@supplierDelete');
        $api->post('supplier-detail', 'SupplierController@supplierDetail');
        $api->get('supplier-order', 'SupplierController@supplierOrderList');
        $api->post('supplier-ordersend', 'SupplierController@supplierOrderSend');
    });
});