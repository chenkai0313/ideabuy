<?php
/**
 * 消息模板Model
 * Author: CK
 * Date: 2017/8/14
 */
namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class MsgTemplate extends Model
{
    #表名
    protected $table = 'system_msg_template';
    #主键
    protected $primaryKey = 'keyword_id';
    #支持字段批量赋值
    protected $fillable = ['content', 'prepare_node', 'msg_type','msg_tag','msg_title'];
    #不支持字段批量赋值
    protected $guarded = ['keyword_id'];

    /**
     * 添加消息模板
     * @param string $param ['content']        消息内容
     * @param string $param ['prepare_node']   预发节点
     * @param string $param ['msg_type']   消息类型
     *  @param string $param ['msg_title']   消息标题
     * @return array
     */
    public static function msgTemplateAdd($params)
    {
        $MsgTemplate['content'] = $params['content'];
        $MsgTemplate['prepare_node'] = $params['prepare_node'];
        $MsgTemplate['msg_tag'] = $params['msg_tag'];
        $MsgTemplate['msg_type'] = $params['msg_type'];
        $MsgTemplate['msg_title'] = $params['msg_title'];
       return MsgTemplate::create($MsgTemplate);
    }

    /**
     * 查看单个短信模板详情
     * @param string $param ['id']  消息模板的ID
     * @return array
     */
    public static function msgTemplateDetail($params)
    {
        $detail = MsgTemplate::where('id', '=', $params)->first();
        return $detail;
    }

    /**
     * 修改短信模板
     * @param string $param ['content']   短信内容
     * @param string $param ['prepare_node']   预发节点
     * @param string $param ['msg_type']   消息类型
     * @param string $param ['msg_tag']   消息标签
     * @return array
     */
    public static function msgTemplateEdit($id, $params)
    {
        $sms = MsgTemplate::where('id', $id)->update($params);
        return $sms;
    }

    /**
     * 查询所有短信模板
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function msgTemplateList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        return MsgTemplate::Search($params)
            ->select('*')
            ->orderBy('id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    public static function msgTemplateCount($params)
    {
        return MsgTemplate::Search($params)
            ->count();
    }

    #查询构造器 Like
    public function scopeSearch($query, $params)
    {
        if (isset($params['keyword'])) {
            return $query->where('system_msg_template.msg_tag', 'like', '%' . $params['keyword'] . '%')
                ->orwhere('system_msg_template.content', 'like', '%' . $params['keyword'] . '%')
               ->orwhere('system_msg_template.msg_type', 'like', '%' . $params['keyword'] . '%');
        }
    }
    /**
     * 删除消息模板
     * @param $params ['id'] 消息模板的ID
     * @return array
     */
    public static function msgTemplateDelete($params){
        $delete=MsgTemplate::where('id',$params['id'])->delete();
        return $delete;
    }

    /**
     * 查询消息模版
     * @param $tag
     * @return mixed
     *
     * @author  liyongchuan
     *
     */
    public static function msgTemplateFirst($tag)
    {
        return MsgTemplate::where('msg_tag',$tag)->first();
    }

}