<?php
/**
 * 内容管理逻辑层 类型 文章 后台
 * Author: 曹晗
 * Date: 2017/7/25
 */
namespace Modules\System\Services;

use Modules\System\Models\ArticleType;
use Modules\System\Models\Article;

class BackendArticleService
{
    /**
     * 添加类型
     * @param string $param['type_name']   类型名称
     * @param int $param['parent_id']   父级ID
     * @return array
     */
    public function articleTypeAdd($param){
        $result = ['code'=>10020,'msg'=>"添加失败"];
        $validator=\Validator::make(
            $param,
            \Config::get('validator.system.articletype.articletype-add'),
            \Config::get('validator.system.articletype.articletype-key'),
            \Config::get('validator.system.articletype.articletype-val')
        );
        if(!$validator->passes()){
            return ['code'=>90002,'msg'=>$validator->messages()->first()];
        }

        $addInfo = ArticleType::articleTypeAdd($param);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }

        return $result;
    }

    /**
     * 删除类型
     * @param int $param['type_id']   类型ID
     * @return array
     */
    public function articleTypeDelete($param) {
        $result = ['code'=>10021,'msg'=>"删除失败"];

        if(!isset($param['type_id']))
            return ['code'=>90002,'msg'=>'请输入类型ID'];

        $CantDelete = ArticleType::articleTypeCanDelte();
        if (strpos($param['type_id'], ',')) {
            $param['type_id'] = explode(',', $param['type_id']);
            $param['type_id'] = array_diff($param['type_id'], $CantDelete);
        } else if (in_array($param['type_id'], $CantDelete)) {
            $result['code'] = 10022;
            $result['msg'] = "删除失败，请先删除子类型";
            return $result;
        }
        $res = ArticleType::articleTypeDelete($param);
        if ($res > 0) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }

        return $result;
    }

    /**
     * 编辑类型
     * @param int $param['type_id']   类型id 必填
     * @param int $param['type_name']   类型名称 可选
     * @param int $param['parent_id']   父级ID 可选
     * @return array
     */
    public function articleTypeEdit($param) {
        $result = ['code'=>10023,'msg'=>"更新失败"];

        $validator=\Validator::make(
            $param,
            \Config::get('validator.system.articletype.articletype-edit'),
            \Config::get('validator.system.articletype.articletype-key'),
            \Config::get('validator.system.articletype.articletype-val')
        );
        if(!$validator->passes()){
            return ['code'=>90002,'msg'=>$validator->messages()->first()];
        }

        $type_id = $param['type_id'];
        unset($param['type_id']);
        unset($param['s']);//清除数组多余的参数
        if ($type_id != $param['parent_id']) {//父id不能是自己
            if ($param['parent_id'] != 0) {
                $articleParent = ArticleType::articleTypeDetail($param['parent_id']);
                if (($type_id == $articleParent['parent_id'])) {
                    $result['code'] = 10024;
                    $result['msg'] = "父级ID有错误，更新失败";
                } else {
                    $editinfo = ArticleType::articleTypeEdit($type_id, $param);
                    if ($editinfo == 1) {
                        $result['code'] = 1;
                        $result['msg'] = "更新成功";
                    }
                }
            } else {
                $editinfo = ArticleType::articleTypeEdit($type_id, $param);
                if ($editinfo == 1) {
                    $result['code'] = 1;
                    $result['msg'] = "更新成功";
                }
            }
        } else {
            $result['code'] = 10025;
            $result['msg'] = "父级不能是自己";
        }

        return $result;
    }

    /**
     * 查询所有类型
     * @param $params['limit'] 一页的数据
     * @param $params['page'] 当前页
     * @param $params['keyword'] 查询关键字 可为空
     * @return array
     */
    public function articleTypeList($params) {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $articleTypeList = ArticleType::articleTypeList($params);
        $data['articletype_list'] = $articleTypeList;
        $data['total'] = ArticleType::articleTypeCount($params);
        return ['code' => 1, 'data' => $data];
    }

    private function GetTree($arr,$pid,$step){
        global $tree;
        foreach($arr as $key=>$val) {
            if($val['parent_id'] == $pid) {
                $flg = str_repeat('— ',$step);
                $val['type_name'] = $flg.$val['type_name'];
                $tree[] = $val;
                $this->GetTree($arr , $val['type_id'] ,$step+1);
            }
        }
        return $tree;
    }

    /**
     * 查询单条类型
     * @param int $param['type_id'] 类型ID 必选
     * @return array
     */
    public function articleTypeDetail($param) {
        if(!isset($param['type_id']))
            return ['code'=>90002,'msg'=>'请输入类型ID'];

        $result['data']['articletype_info'] = ArticleType::articleTypeDetail($param['type_id']);
        $result['code'] = 1;
        $result['msg'] = "查询成功";

        return $result;
    }

    /**
     * 查询所有类型  下拉框用
     */
    public function articleTypeSelect() {
        $articletype_select = ArticleType::articleTypeSelect();
        $newarr = $this->GetTree($articletype_select,0,0);
        $result['data']['articletype_select'] = $newarr;
        $result['code'] = 1;
        $result['msg'] = "查询成功";
        return $result;
    }

    #操作文章方法
    /**
     * 添加文章
     * @param string $param['type_name']   类型名称
     * @param string $param['article_title']   标题
     * @param int $param['parent_id']   父级ID
     * @return array
     */
    public function articleAdd($param) {
        $validator=\Validator::make(
            $param,
            \Config::get('validator.system.article.article-add'),
            \Config::get('validator.system.article.article-key'),
            \Config::get('validator.system.article.article-val')
        );
        if(!$validator->passes()){
            return ['code'=>90002,'msg'=>$validator->messages()->first()];
        }

        $result = ['code'=>10026,'msg'=>"添加失败"];
        $param['admin_id'] = isset($param['admin_id'])?$param['admin_id']:0;
        $param['article_content'] = isset($param['article_content'])?$param['article_content']:"";
        $addInfo = Article::articleAdd($param);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }

        return $result;
    }

    /**
     * 删除内容
     * @param int $param['article_id']   内容ID
     * @return array
     */
    public function articleDelete($params) {
        if(!isset($params['article_id']))
            return ['code'=>90002,'msg'=>'请输入文章ID'];

        if (strpos($params['article_id'], ',')) {
            $params['article_id'] = explode(',', $params['article_id']);
        };
        $result = ['code'=>10027,'msg'=>"删除失败"];
        $delinfo = Article::articleDelete($params);
        if ($delinfo > 0) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }

        return $result;
    }

    /**
     * 编辑内容
     * @param int $article_id 文章ID
     * @param int $param['type_id']   类型ID 可选
     * @param text $param['article_id']   文章内容 可选
     * @param text $param['article_title']   文章标题 可选
     * @param text $param['admin_id']   操作员ID 可选
     * @return array
     */
    public function articleEdit($param) {
        $validator=\Validator::make(
            $param,
            \Config::get('validator.system.article.article-edit'),
            \Config::get('validator.system.article.article-key'),
            \Config::get('validator.system.article.article-val')
        );
        if(!$validator->passes()){
            return ['code'=>90002,'msg'=>$validator->messages()->first()];
        }

        $result = ['code'=>10028,'msg'=>"更新失败"];
        $article_id = $param['article_id'];
        unset($param['article_id']);unset($param['s']);//清除数组多余的参数
        $editinfo = Article::articleEdit($article_id,$param);
        if ($editinfo == 1) {
            $result['code'] = 1;
            $result['msg'] = "更新成功";
        }

        return $result;
    }

    /**
     * 查询单条信息
     * @param int $article_id 文章ID
     * @return array
     */
    public function articleDetail($param) {
        if(!isset($param['article_id']))
            return ['code'=>90002,'msg'=>'请输入文章ID'];

        $article_id = $param['article_id'];
        $result['data']['article_info'] = Article::articleDetail($article_id);
        $result['code'] = 1;
        $result['msg'] = "查询成功";
        return $result;
    }

    /**
     * 查询所有文章
     * @param $params['limit'] 一页的数据
     * @param $params['page'] 当前页
     * @param $params['keyword'] 查询关键字 可为空
     * @return array
     */
    public function articleList($params) {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['article_list'] = Article::articleList($params);
        $data['total'] = Article::articleCount($params);
        return ['code' => 1, 'data' => $data];
    }

}