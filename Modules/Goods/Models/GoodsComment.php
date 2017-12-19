<?php
/**
 * 评论表
 * Author: 陈凯
 * Date: 2017/10/12
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\Models\OrderGoods;
use Modules\Backend\Models\Admin;

class GoodsComment extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'goods_comment';

    protected $primaryKey = 'comment_id';

    public $fillable = ['comment_id', 'goods_id', 'product_id', 'user_id', 'comment_type', 'comment_star', 'comment_desc',
        'comment_pics', 'comment_extra_desc', 'comment_repay', 'order_sn', 'admin_id', 'goods_key'];


    /**
     * 添加评论
     * @params comment_id  int     评论ID
     * @params goods_id    int     商品ID
     * @params product_id  int     货品ID
     * @params user_id     int     用户ID
     * @params comment_type  int   评论类型
     * @params comment_star  int   评论星级
     * @params comment_pics  string   评论图片
     * @params comment_desc  string   评论内容
     * @params comment_extra_desc  string   追评内容
     * @params comment_repay  string   评论回复
     * @return mixed
     *
     * @author CK
     */
    public static function commentAdd($params)
    {
        return GoodsComment::create($params);
    }

    /**
     * 评论列表
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function commentListAll($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        $data = GoodsComment::leftJoin('users', 'users.user_id', '=', 'goods_comment.user_id')
            ->leftJoin('goods_products', 'goods_products.product_id', '=', 'goods_comment.product_id')
            ->leftJoin('goods', 'goods.goods_id', '=', 'goods_comment.goods_id')
            ->Search($params)
            ->select('goods_comment.comment_id', 'goods_comment.goods_id', 'goods_comment.product_id', 'goods_comment.user_id',
                'goods_comment.comment_type', 'goods_comment.comment_star', 'goods_comment.comment_pics', 'goods_comment.comment_pics',
                'goods_comment.comment_desc', 'goods_comment.comment_extra_desc', 'goods_comment.admin_id', 'goods_comment.order_sn',
                'goods_comment.comment_repay', 'goods_comment.created_at', 'goods_comment.goods_key',
                'goods_comment.updated_at', 'users.real_name', 'goods_products.product_name', 'goods.goods_name')
            ->orderBy('comment_id', 'desc')
            ->skip($offset)
            ->take($params['limit'])
            ->get();
        return $data;
    }

    public static function commentCount($params)
    {
        return GoodsComment::leftJoin('users', 'users.user_id', '=', 'goods_comment.user_id')
            ->leftJoin('goods_products', 'goods_products.product_id', '=', 'goods_comment.product_id')
            ->leftJoin('goods', 'goods.goods_id', '=', 'goods_comment.goods_id')
            ->Search($params)
            ->count();
    }

    public function scopeSearch($query, $params)
    {
        if (isset($params['keyword'])) {
            return $query->where('users.real_name', 'like', '%' . $params['keyword'] . '%')
                ->orwhere('goods_products.product_name', 'like', '%' . $params['keyword'] . '%')
                ->orwhere('goods.goods_name', 'like', '%' . $params['keyword'] . '%')
                ->orwhere('order_sn', '=', $params['keyword']);
        }
    }


    /**
     * 评论详情
     * @params comment_id  int     评论ID
     * @params goods_id    int     商品ID
     * @params product_id  int     货品ID
     * @params user_id     int     用户ID
     * @params comment_type  int   评论类型
     * @params comment_star  int   评论星级
     * @params comment_pics  string   评论图片
     * @params comment_desc  string   评论内容
     * @params comment_extra_desc  string   追评内容
     * @params comment_repay  string   评论回复
     * @return mixed
     *
     * @author CK
     */
    public static function commentDetail($params)
    {
        return GoodsComment::select('comment_id', 'goods_id', 'product_id', 'user_id', 'comment_type', 'comment_star', 'comment_pics'
            , 'comment_desc', 'comment_extra_desc', 'comment_repay', 'updated_at', 'created_at', 'repay_at', 'admin_id')
            ->where('comment_id', '=', $params['comment_id'])->first();
    }

    /**
     * 追加评论
     * @params user_id     int     用户ID
     * @params comment_id  int     评论ID
     * @params comment_extra_desc   追评价内容
     * @return mixed
     *
     * @author CK
     */
    public static function commentEdit($params)
    {
        $data = GoodsComment::find($params['comment_id']);
        $data->comment_extra_desc = $params['comment_extra_desc'];
        $result = $data->update();
        return $result;
    }

    /**
     * 订单商品详情
     * @return mixed
     *
     * @author CK
     */
    public static function orderDeatil($params)
    {
        $order = OrderGoods::select('id', 'order_sn', 'goods_name', 'goods_thumb', 'attr_name', 'attr_value')
            ->where('goods_key', '=', $params)->first();
        #商品属性字符串
        $str_attr = '';
        if ($order['attr_name']) {
            $temp_attr_name = explode("|", $order['attr_name']);
            $temp_attr_value = explode("|", $order['attr_value']);
            $j = count($temp_attr_name);
            for ($i = 0; $i < $j; $i++) {
                $str_attr .= $temp_attr_name[$i] . ":" . $temp_attr_value[$i] . ' ';
            }
        }
        $order['str_attr'] = $str_attr;
        unset($order['attr_name']);
        unset($order['attr_value']);
        return $order;
    }

    /**
     * 回复评论
     * @return mixed
     *
     * @author CK
     */
    public static function commentRepay($params)
    {
        $data = GoodsComment::find($params['comment_id']);
        $data->comment_repay = $params['comment_repay'];
        $data->admin_id = $params['admin_id'];
        $data->repay_at = date("Y-m-d H:i:s", time());
        $result = $data->update();
        return $result;
    }

    /**
     * 查看当前商品的所有评论
     * @return mixed
     *
     * @author CK
     */
    public static function commentListProduct($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        $data = GoodsComment::Like($params)
            ->select('*')
            ->where('goods_id', '=', $params['goods_id'])
            ->orderBy('comment_id', 'desc')
            ->skip($offset)
            ->take($params['limit'])
            ->get();
        return $data;
    }

    public static function commentListProductCount($params)
    {
        return GoodsComment::Like($params)->where('goods_id', '=', $params['goods_id'])->count();
    }

    public function scopeLike($query, $params)
    {
        if (isset($params['star']) && empty($params['is_pics'])) {
            return $query->where('comment_star', '=', $params['star']);
        }
        if (isset($params['is_pics']) && empty($params['star'])) {
            return $query->where('comment_pics', '!=', null);
        }
    }
    /**
     *删除评论（支持批量删除）
     * @author CK
     */

    public static function commentDelete($params) {
        $res = GoodsComment::destroy($params);
        return $res;
    }
    /**
     *当前供应商下的商品所有评论
     * @author CK
     */
    public static function commentListAdmin($params){
        $offset = ($params['page'] - 1) * $params['limit'];
        $admin_goods=Goods::select('goods_id')->where('admin_id',$params['admin_id'])->get()->toArray();
        $arr=array();
        foreach($admin_goods as $v){
            $arr[]=$v['goods_id'];
        }
        $data = GoodsComment::leftJoin('users', 'users.user_id', '=', 'goods_comment.user_id')
            ->leftJoin('goods_products', 'goods_products.product_id', '=', 'goods_comment.product_id')
            ->leftJoin('goods', 'goods.goods_id', '=', 'goods_comment.goods_id')
            ->Search($params)
            ->select('goods_comment.comment_id', 'goods_comment.goods_id', 'goods_comment.product_id', 'goods_comment.user_id',
                'goods_comment.comment_type', 'goods_comment.comment_star', 'goods_comment.comment_pics', 'goods_comment.comment_pics',
                'goods_comment.comment_desc', 'goods_comment.comment_extra_desc', 'goods_comment.admin_id', 'goods_comment.order_sn',
                'goods_comment.comment_repay', 'goods_comment.created_at', 'goods_comment.goods_key',
                'goods_comment.updated_at', 'users.real_name', 'goods_products.product_name', 'goods.goods_name')
            ->orderBy('comment_id', 'desc')
            ->skip($offset)
            ->whereIn('goods_comment.goods_id',$arr)
            ->take($params['limit'])
            ->get();
        return $data;
    }

}
