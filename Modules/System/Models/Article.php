<?php
/**
 * 文章Model
 * Author: 曹晗
 * Date: 2017/7/25
 */
namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Article extends Model
{
    use SoftDeletes;
    protected $table = 'system_article';
    protected $primaryKey = 'article_id';
    protected $fillable = ['type_id','article_content','admin_id','article_title','sort'];

    /**
     * 添加类型
     * @param int $param['type_id']   类型ID
     * @param text $param['article_content'] 文章内容
     * @param int $param['admin_id']   操作员ID
     * @return array
     *
     * @auth 曹晗
     */
    public static function articleAdd($param) {
        $article['type_id'] = $param['type_id'];
        $article['article_title'] = $param['article_title'];
        $article['article_content'] = $param['article_content'];
        $article['admin_id'] = $param['admin_id'];
        $article['sort'] = isset($param['sort'])?$param['sort']:0;
        return Article::create($article);
    }

    /**
     * 删除内容
     * @param int $param['article_id']   内容ID
     * @return array
     */
    public static function articleDelete($params) {
        return Article::destroy($params['article_id']);
    }

    /**
     * 编辑内容
     * @param int $article_id 文章ID
     * @param int $param['type_id']   类型ID 可选
     * @param text $param['article_id']   文章内容 可选
     * @param text $param['admin_id']   操作员ID 可选
     * @return array
     */
    public static function articleEdit($article_id,$param) {
        $article = Article::where('article_id',$article_id )->update($param);
        return $article;
    }

    /**
     * 查询单条信息
     * @param int $article_id 文章ID
     * @return array
     */
    public static function articleDetail($article_id) {
        $article = Article::leftJoin('system_article_type','system_article_type.type_id','=','system_article.type_id')
            ->where('system_article.article_id',$article_id)
            ->select('article_id','system_article.type_id','article_content','admin_id','type_name','article_title')
            ->first();
        return $article;
    }

    /**
     * 查询所有类型
     * @param $params['limit'] 一页的数据
     * @param $params['page'] 当前页
     * @param $params['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function articleList($params) {
        $offset=($params['page']-1)*$params['limit'];
        return Article::leftJoin('system_article_type','system_article_type.type_id','=','system_article.type_id')
            ->Search($params)
            ->select('article_id','system_article.type_id','admin_id','type_name','parent_id','article_title')
            ->orderBy('type_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    public static function articleCount($params)
    {
        return Article::leftJoin('system_article_type','system_article_type.type_id','=','system_article.type_id')
            ->Search($params)
            ->count();
    }

    #查询构造器 Like
    public function scopeSearch($query,$params)
    {
        if (isset($params['keyword'])){
            return $query->where('system_article.article_title', 'like', '%'.$params['keyword'].'%');
        }
    }
}
