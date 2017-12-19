<?php

namespace Modules\Supplier\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SupplierController extends Controller
{
    /**
     * 供应商添加
     */
    public function supplierAdd(Request $request){
        $params = $request->input();
        return \SupplierService::supplierAdd($params);
    }
    /**
     * 供应商编辑
     */
    public function supplierEdit(Request $request){
        $params = $request->input();
        return \SupplierService::supplierEdit($params);
    }
    /**
     * 供应商删除
     */
    public function supplierDelete(Request $request){
        $params = $request->input();
        return \SupplierService::supplierDelete($params);
    }
    /**
     * 供应商列表
     */
    public function supplierList(Request $request){
        $params = $request->input();
        return \SupplierService::supplierList($params);
    }
    /**
     * 供应商详情
     */
    public function supplierDetail(Request $request){
        $params = $request->input();
        return \SupplierService::supplierDetail($params);
    }
    /**
     * 供应商登录
     */
    public function supplierLogin(Request $request){
        $params = $request->input();
        $params['login_ip'] = $request->getClientIp();
        return \SupplierService::supplierLogin($params);
    }
    /**
     * 供应商订单查询
     */
    public function supplierOrderList(Request $request)
    {
        $params = $request->input();
        $params['supplier_id'] = get_supplier_id();
        return \SupplierService::supplierOrderList($params);
    }
    /**
     * 供应商订单发货
     */
    public function supplierOrderSend(Request $request)
    {
        $params = $request->input();
        $params['supplier_id'] = get_supplier_id();
        return \SupplierService::supplierOrderSend($params);
    }

}
