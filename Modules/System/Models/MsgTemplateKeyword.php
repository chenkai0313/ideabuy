<?php
/**
 * 消息模板关键字Model
 * Author: CK
 * Date: 2017/8/14
 */
namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class MsgTemplateKeyword extends Model
{
    #表名
    protected $table = 'system_msg_template_info';
    #主键
    protected $primaryKey = 'keyword_id';
    #支持字段批量赋值
    protected $fillable = ['keyword_name','keyword_zh'];
    #不支持字段批量赋值
    protected $guarded = ['keyword_id'];

    /**
     * 添加是否添加过此关键字
     * @param string $param ['keyword_name']   关键字
     * @return array
     */
    public static function msgTemplateKeywordHad($params)
    {
        $had = MsgTemplateKeyword::where('keyword_name', '=', $params['keyword_name'])->first();
        return $had;
    }

    /**
     * 添加关键字
     * @param string $param ['keyword_name']   关键字
     * @return array
     */
    public static function msgTemplateKeywordAdd($params)
    {
        $MsgTemplateKeyword['keyword_name'] = $params['keyword_name'];
        $MsgTemplateKeyword['keyword_zh'] = $params['keyword_zh'];
        return MsgTemplateKeyword::create($MsgTemplateKeyword);
    }

    /**
     * 查询单条关键字详情
     * @param int $keyword_id 文章ID
     * @return array
     */
    public static function msgTemplateKeywordDetail($keyword_id)
    {
        $detail = MsgTemplateKeyword::where('keyword_id', '=', $keyword_id)->first();
        return $detail;
    }

    /**
     * 修改单个关键字
     * @param string $param ['keyword_name']   关键字
     * @return array
     */
    public static function msgTemplateKeywordEdit($keyword_id, $params)
    {
        $keyword = MsgTemplateKeyword::where('keyword_id', $keyword_id)->update($params);
        return $keyword;
    }


    /**
     * 查询所有关键字
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function msgTemplateKeywordList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        return MsgTemplateKeyword::Search($params)
            ->select('*')
            ->orderBy('keyword_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    public static function msgTemplateKeywordCount($params)
    {
        return MsgTemplateKeyword::Search($params)
            ->count();
    }

    #查询构造器 Like
    public function scopeSearch($query, $params)
    {
        if (isset($params['keyword'])) {
            return $query->where('system_msg_template_info.keyword_name', 'like', '%' . $params['keyword'] . '%');
        }
    }

    /**
     * 查询所有关键字
     * @param $params ['keyword_id '] 关键字的id
     * @return array
     */
    public static function msgTemplateKeywordDelete($params)
    {
        $delete = MsgTemplateKeyword::where('keyword_id', $params['keyword_id'])->delete();
        return $delete;
    }

}

