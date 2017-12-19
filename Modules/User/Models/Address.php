<?php
/**
 * 地址Model
 * Author: CK
 * Date: 2017/8/4
 */
namespace Modules\User\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\System\Services\RegionService;
use Modules\System\Models\Region;

class Address extends Model
{

    #表名
    protected $table = 'user_address';
    #主键
    protected $primaryKey = 'address_id';
    #支持字段批量赋值
    protected $fillable = ['user_id', 'province', 'city', 'district', 'street', 'address','consignee','mobile'];
    #不支持字段批量赋值
    protected $guarded = ['address_id'];

    /**
     *判断是否添加过地址
     * @param $params ['user_id']  int  用户ID
     * @return $this|Model
     */

    public static function userAddressHadAdd($params)
    {
        $had =Address::where('user_id',$params['user_id'])->get();
        return $had;
    }

    /**
     *判断用户的操作是否违规
     * @param $params ['address_id']  int  地址ID
     * @return $this|Model
     */
    public static function userConfirm($params){
        $had =Address::where('address_id',$params['address_id'])->first();
        return $had;
    }


    /**
     *地址新增
     * @param $params ['user_id']   int  用户ID
     * @param $params ['province']  int  省
     * @param $params ['city']  int  市
     * @param $params ['district']  int  区
     * @param $params ['street']  int  街道
     * @param $params ['address']  string  详细地址
     * @return $this|Model
     */

    public static function userAddressAdd($params)
    {
        return Address::create($params);
    }

    /**
     * 删除内容
     * @param int $param ['address_id']   地址ID
     * @return array
     */
    public static function userAddressDelete($params)
    {
        return Address::destroy($params['address_id']);
    }

    /**
     * 查询单条信息
     * @param int $article_id 文章ID
     * @return array
     */
    public static function userAddressDetail($address_id)
    {
        $address =Address::where('address_id',$address_id)->first();
        $aa = new RegionService();
        $params = array();
        $params['province'] = $address['province'];
        $params['city'] = $address['city'];
        $params['district'] = $address['district'];
        $aa = $aa->regionGet($params);
        $address['province_name'] = $aa['data']['province'];
        $address['city_name'] = $aa['data']['city'];
        $address['district_name'] = $aa['data']['district'];
        $user_name=User::where('user_id',$address['user_id'])->first();
        $address['user_name']=$user_name['real_name'];
        $address['tel']=$user_name['user_mobile'];
        return $address;
    }

    /**
     * 查询用户收货地址
     * @param int $article_id 文章ID
     * @return array
     */
    public static function userAddressList($user_id)
    {
        $address = Address::where('user_id',$user_id)->get();
        $user = User::where('user_id',$user_id)->first();
        $defalut = $user['address_id'];
        if($address){
            foreach ($address as $k=>$v){
                $region_info = \RegionService::regionGet(['province'=>$v['province'],'city'=>$v['city'],'district'=>$v['district']]);
                $address[$k]['province_name'] = $region_info['data']['province'];
                $address[$k]['city_name'] = $region_info['data']['city'];
                $address[$k]['district_name'] = $region_info['data']['district'];
                if($address[$k]['address_id']==$defalut){
                    $address[$k]['default']=1;
                }else{
                    $address[$k]['default']=0;
                }
            }
        }

        return $address;
    }
    /**
     * 查询单条信息
     * @param int $article_id 文章ID
     * @return array
     */
    public static function userAddressDefault($params)
    {
        $res=User::where('user_id',$params['user_id'])->first();
        $res->address_id=$params['address_id'];
        $res->save();
        return $res;
    }



    /**
     * 编辑内容
     * @param $params ['user_id']   int  用户ID
     * @param $params ['province']  int  省
     * @param $params ['city']  int  市
     * @param $params ['district']  int  区
     * @param $params ['street']  int  街道
     * @param $params ['address']  string  详细地址
     * @return array
     */
    public static function userAddressEdit($address_id, $params)
    {
        $address=Address::find($address_id);
        $address->province=$params['province'];
        $address->city=$params['city'];
        $address->district=$params['district'];
        $address->address=$params['address'];
        $address->consignee=$params['consignee'];
        $address->mobile=$params['mobile'];
        $data=$address->save();
        return $data;
    }


}
