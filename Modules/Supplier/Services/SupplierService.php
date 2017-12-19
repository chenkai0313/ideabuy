<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/26
 * Time: 16:40
 */
namespace Modules\Supplier\Services;

use Modules\Order\Models\OrderInfo;
use Modules\Supplier\Models\Supplier;
use JWTAuth;

class SupplierService
{
    /**
     * 供应商 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @param int $keyword 查询关键词
     * @return array
     */
    public function supplierList($params)
    {
        $res = Supplier::supplierList($params);
        $result['data']['supplier_list'] = $res['list'];
        $result['data']['total'] = $res['total'];
        $result['data']['pages'] = $res['pages'];
        $result['code'] = 1;
        return $result;
    }
    /**
     * 供应商添加
     * @param string $supplier_mobile 供应商手机（登录账号）
     * @param string $supplier_name 供应商名称
     * @param string $supplier_password 供应商登录密码
     * @param string $province 省
     * @param string $city 市
     * @param string $district 区
     * @param string $address 详细地址
     * @param string $remark 备注
     * @return array
     */
    public function supplierAdd($params)
    {
        $validator = \Validator::make(
            $params,
            config('validator.supplier.supplier.supplier-add'),
            config('validator.supplier.supplier.supplier-key'),
            config('validator.supplier.supplier.supplier-val')
            );
        if($validator->fails()){
            return ['code' => 90002,'msg' => $validator->messages()->first()];
        }
        if(Supplier::supplierExist($params)){
            return ['code' => 30001 , 'msg' => '该供应商已存在'];
        }
        $add_res= Supplier::supplierAdd($params);
        if($add_res){
            $result['code'] = 1;
            $result['msg'] = '添加成功';
        }
        return $result;
    }
    /**
     * 供应商编辑
     * @param  $supplier_id 供应商ID
     * @param string $supplier_mobile 供应商手机（登录账号）
     * @param string $supplier_name 供应商名称
     * @param string $supplier_password 供应商登录密码
     * @param string $province 省
     * @param string $city 市
     * @param string $district 区
     * @param string $address 详细地址
     * @param string $remark 备注
     * @return array
     */
    public function supplierEdit($params)
    {
        if(!isset($params['supplier_id'])){
            return ['code' => 90001 ,'msg' => '供应商ID不能为空'];
        }else {
            $had = Supplier::find($params['supplier_id']);
            if (is_null($had)) {
                return ['code' => 30004, 'msg' => '该供应商不存在'];
            }
            $validator = \Validator::make(
                $params,
                config('validator.supplier.supplier.supplier-edit'),
                config('validator.supplier.supplier.supplier-key'),
                config('validator.supplier.supplier.supplier-val')
            );
            if ($validator->fails()) {
                return ['code' => 90002, 'msg' => $validator->messages()->first()];
            }
            if (Supplier::supplierEdit($params)) {
                return ['code' => 1, 'msg' => '更新成功'];
            }
        }
    }
    /**
     * 供应商删除
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public function supplierDelete($params)
    {
        if(!isset($params['supplier_id'])){
            return ['code' => 90001 ,'msg' => '供应商ID不能为空'];
        }else{
            $had = Supplier::find($params['supplier_id']);
            if(is_null($had)){
                return ['code' => 30004 ,'msg' => '该供应商不存在'];
            }else{
                if(Supplier::supplierDelete($params)){
                    return ['code' => 1, 'msg' => '删除成功'];
                }
            }
        }


    }
    /**
     * 供应商详情
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public function supplierDetail($params)
    {
        if(!isset($params['supplier_id'])){
            return ['code' => 90001 ,'msg' => '供应商ID不能为空'];
        }else{
            $had = Supplier::find($params['supplier_id']);
            if(is_null($had)){
                return ['code' => 30004 ,'msg' => '该供应商不存在'];
            }else{
                $data = Supplier::supplierDetail($params);
                if($data){
                    return ['code' => 1, 'msg' => '查询成功','data' => $data];
                }
            }
        }
    }
    /**
     * 供应商登录
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public function supplierLogin($params)
    {
        $validator = \Validator::make(
            $params,
            config('validator.supplier.supplier.supplier-login'),
            config('validator.supplier.supplier.supplier-key'),
            config('validator.supplier.supplier.supplier-val')
        );
        if($validator->fails()){
            return ['code' => 30001,'msg' => $validator->messages()->first()];
        }
        #查询该供应商信息
        $supplier_info = Supplier::supplierInfo($params);
        if($supplier_info){
            if(password_verify($params['supplier_password'],$supplier_info['supplier_password'])){
                #更新IP地址
                $update_info = array('supplier_id' => $supplier_info['supplier_id'] , 'login_ip' => $params['login_ip']);
                $result = Supplier::supplierLogin($update_info);
                try{
                    $result = Supplier::supplierLogin($update_info);
                    if($result){
                        $res['code'] = 1;
                        $res['msg'] = '登录成功';
                        $customClaim = ['from' => 'supplier','supplier_id' => $supplier_info['supplier_id'],'supplier_name' => $supplier_info['supplier_name']];
                        $token = JWTAuth::fromUser($supplier_info,$customClaim);
                        $res['data']['token'] = $token;
                        $res['data']['supplier_name'] = $supplier_info['supplier_name'];
                    }
                }catch (\Exception $exception){
                    return ['code' => 30002 ,'msg' => $exception->getMessage()];
                }
            }
            else{
                $res['code'] = 30003;
                $res['msg'] = '账号密码不正确';
            }
        }
        else{
            $res['code'] = 30004;
            $res['msg'] = '该供应商账号不存在或已删除';
        }
        return $res;
    }
    /**
     * 供应商订单查询
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public function supplierOrderList($params)
    {
        if($params['supplier_id']){
            $res = OrderInfo::supplierOrderList($params);
            $result['data']['order_list'] = $res['list'];
            $result['data']['total'] = $res['total'];
            $result['data']['pages'] = $res['pages'];
            $result['code'] = 1;
            return $result;
        }else{
            return ['code' => 90001 ,'msg' => '供应商ID不能为空'];
        }

    }
    /**
     * 供应商发货
     * @param int $order_id 订单ID
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public function supplierOrderSend($params)
    {
        if(!isset($params['order_id'])){
            return ['code' => 90001,'msg' => '订单id必填'];
        }
        else{
            $order = OrderInfo::find($params['order_id']);
            if(is_null($order)){
                return ['code' => 10260,'msg' => '未找到该订单'];
            }
            if($order['supplier_id'] == $params['supplier_id']){
                if($order['order_status'] == 1){
                        if (OrderInfo::supplierOrderSend($params)) {
                            return ['code' => 1, 'msg' => '发货成功'];
                        }
                }
                else{
                    return ['code' => 10261,'msg' => '该订单状态无法发货'];
                }
            }
            else{
                return ['code' => 90004,'msg' => '该订单无权更改'];
            }
        }

    }
}