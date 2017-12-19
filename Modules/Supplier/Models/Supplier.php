<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/26
 * Time: 14:43
 */

namespace Modules\Supplier\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\System\Services\RegionService;

class Supplier extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'supplier';

    protected $primaryKey = 'supplier_id';

    protected $fillable = ['supplier_id', 'supplier_mobile', 'supplier_name', 'supplier_password', 'province','city','district','address','login_ip','login_at','remark'];

    protected $dates = ['deleted_at'];
    /**
     * 供应商 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @param int $keyword 查询关键词
     * @return array
     */
    public static function supplierList($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $ads = new RegionService();
        $data = Supplier::select(['supplier_id', 'supplier_mobile', 'supplier_name', 'province','city','district','address','login_ip','login_at','remark'])
            ->where('supplier_name' , 'like' , '%' . strip_tags($params['keyword']) . '%' )
            ->orWhere(function ($query) use ($params){
                $query->where('supplier_mobile' , 'like' , '%' . strip_tags($params['keyword']) . '%');
            })
            ->orderByDesc('supplier_id')
            ->orderByDesc('updated_at')
            ->paginate($limit);
        $res['total'] = $data->total();
        $res['pages'] = ceil($data->total() / $limit);
        $res['list'] = $data->items();
        return $res;

    }
    /**
     * 供应商  添加
     * @param string $supplier_moblie 供应商手机号
     * @param string $supplier_password 密码
     * @return array
     */
    public static function supplierAdd($params)
    {
        $params['supplier_password'] = $params['supplier_password'] ? bcrypt($params['supplier_password']) : bcrypt('111111');
        $res = Supplier::create($params);

        return $res->supplier_id;
    }
    /**
     * 供应商  编辑
     * @param int $supplier_id 供应商ID
     * @param string $supplier_password 密码
     * @return bool
     */
    public static function supplierEdit($params){
        $data = Supplier::find($params['supplier_id']);
        if(!empty($params['supplier_password']))
        {
            $data->supplier_password = bcrypt($params['supplier_password']);
        }

        $data->supplier_name = $params['supplier_name'];
        $data->supplier_mobile = $params['supplier_mobile'];
        $data->province = $params['province'];
        $data->city = $params['city'];
        $data->district = $params['district'];
        $data->address = $params['address'];
        $data->remark = $params['remark'];
        $res = $data->save();

        return $res;
    }
    /**
     * 供应商  详情
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public static function supplierDetail($params)
    {

        $data = Supplier::select(['supplier_id', 'supplier_mobile', 'supplier_name', 'province','city','district','address','login_ip','login_at','remark'])->where('supplier_id','=',$params['supplier_id'])->first();
        #用户地址翻译
        $RegionService = new RegionService();
        $aa = array();
        $aa['province'] = $data['province'];
        $aa['city'] = $data['city'];
        $aa['district'] = $data['district'];
        $RegionService = $RegionService->regionGet($aa);
        $data['province'] = $RegionService['data']['province'];
        $data['city'] = $RegionService['data']['city'];
        $data['district'] = $RegionService['data']['district'];
        return $data;
    }
    /**
     * 供应商 删除
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public static function supplierDelete($params)
    {
        $supplier_ids = explode(',',$params['supplier_id']);
        $res = Supplier::whereIn('supplier_id',$supplier_ids)->delete();
        return $res;
    }
    /**
     * 检查供应商是否存在
     * @param string $supplier_mobile 供应商手机号
     * @return array
     */
    public static function supplierExist($params)
    {
        $res = Supplier::where('supplier_mobile',$params['supplier_mobile'])
            ->orWhere('supplier_name',$params['supplier_name'])
            ->count();
        return $res;
    }
    /**
     * 供应商登录
     * @param string $supplier_mobile 供应商手机号
     * @param int $supplier_id 供应商ID
     * @param string $supplier_name 供应商名称
     * @return array
     */
    public static function supplierInfo($params)
    {
        $res = Supplier::where('supplier_mobile',$params['supplier_mobile'])
            ->select('supplier_id','supplier_name','supplier_password')
            ->first();
        return $res;
    }
    /**
     * 供应商登录地址IP和登录时间更新
     * @param int $supplier_id 供应商ID
     * @param string $login_ip 登录ip
     * @param string $login_at 登录时间
     * @return bool
     */
    public static function supplierLogin($params)
    {
        $data = Supplier::find($params['supplier_id']);
        $data->login_ip = $params['login_ip'];
        $data->login_at = date('Y-m-d H:i:s',time());
        $res = $data->save();
        return $res;
    }

}