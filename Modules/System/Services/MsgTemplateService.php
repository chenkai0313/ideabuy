<?php
/**
 * 消息模板模块
 * Author: CK
 * Date: 2017/8/12
 */
namespace Modules\System\Services;

use Modules\System\Models\MsgTemplate;
use Modules\System\Models\MsgTemplateKeyword;


class MsgTemplateService
{
    /**
     * 关键字的新增(backend)
     * @param $params ['keyword_name']  string    关键字
     * @return mixed
     */
    public function msgTemplateKeywordAdd($params)
    {
        $result = ['code' => 10020, 'msg' => "添加失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.msgtemplate.msgtemplatetype-add'),
            \Config::get('validator.system.msgtemplate.msgtemplate-key'),
            \Config::get('validator.system.msgtemplate.msgtemplate-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        #关键字的格式添加是否正确
        if (substr($params['keyword_name'], -1) !== "}" || substr($params['keyword_name'], 0, 1) !== "$"
            || substr($params['keyword_name'], 1, 1) !== "{"
        ) {
            return ['code' => 10024, 'msg' => "关键字添加格式不对"];
        }
        #判断是否存在此关键字
        $had = MsgTemplateKeyword::msgTemplateKeywordHad($params);
        if (!empty($had)) {
            return ['code' => 10200, 'msg' => "已经添加过此关键字"];
        }
        $addInfo = MsgTemplateKeyword::msgTemplateKeywordAdd($params);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }
        return $result;
    }

    /**
     * 查询所有关键字
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public function msgTemplateKeywordList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $msgTemplateKeywordList = MsgTemplateKeyword::msgTemplateKeywordList($params);
        $data['msgTemplateKeyword_list'] = $msgTemplateKeywordList;
        $data['total'] = MsgTemplateKeyword::msgTemplateKeywordCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 查询单个关键字
     * @return array
     */
    public function msgTemplateKeywordDetail($params)
    {
        if (!isset($params['keyword_id']))
            return ['code' => 90002, 'msg' => '请输入关键字ID'];
        $result['data']['msgTemplateKeywordDetail_info'] = MsgTemplateKeyword::msgTemplateKeywordDetail($params['keyword_id']);
        $result['code'] = 1;
        $result['msg'] = "查询成功";
        return $result;
    }

    /**
     * 关键字的编辑(backend)
     * @param $params ['keyword_name']  string    分类关键字
     * @return mixed
     */
    public function msgTemplateKeywordEdit($params)
    {
        $result = ['code' => 10023, 'msg' => "更新失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.msgtemplate.msgtemplatetype-edit'),
            \Config::get('validator.system.msgtemplate.msgtemplate-key'),
            \Config::get('validator.system.msgtemplate.msgtemplate-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        #关键字的格式添加是否正确
        if (substr($params['keyword_name'], -1) !== "}" || substr($params['keyword_name'], 0, 1) !== "$"
            || substr($params['keyword_name'], 1, 1) !== "{"
        ) {
            return ['code' => 10024, 'msg' => "关键字添加格式不对"];
        }
        $keyword_id = $params['keyword_id'];
        #判断是否存在此关键字
        $old_name = MsgTemplateKeyword::where('keyword_id', '=', $keyword_id)->first();
        if (empty($old_name)) {
            return ['code' => 10023, 'msg' => "更新失败"];
        }
        unset($params['keyword_id']);
        unset($params['s']);//清除数组多余的参数
        $keyword_name = $params['keyword_name'];
        if ($keyword_name) {
            #如果对应关键字发生改变，关键字所处的短信模板，同时改变
            #查询所有短信模板
            $msgTemplate = MsgTemplate::all();
            #循环所有短信模板
            foreach ($msgTemplate as $k => $v) {
                #将短信模板内容正则，获取关键字
                $content = $v['content'];
                $reg = '/\${[^\}]+\}/';
                preg_match_all($reg, $content, $want);
                #循环关键字
                foreach ($want[0] as $m) {
                    #找到关键字与改之前一样的关键字
                    if ($m == $old_name['keyword_name']) {
                        $now = str_replace(trim($m, '\${\}'), trim($keyword_name, '\${\}'), $content);
                        MsgTemplate::where('id', $v['id'])->update(['content' => $now]);
                    }
                }
            }
            $editinfo = MsgTemplateKeyword::msgTemplateKeywordEdit($keyword_id, $params);
            $result['code'] = 1;
            $result['msg'] = "更新成功";
        }
        return $result;
    }

    /**
     * 关键字的删除(backend)
     * @param $params ['keyword_id']  string    关键字的ID
     * @return mixed
     */
    public function msgTemplateKeywordDelete($params)
    {
        $result = ['code' => 10023, 'msg' => "删除失败"];
        if (!isset($params['keyword_id']))
            return ['code' => 90002, 'msg' => '请输入要删除的关键字的ID'];
        $delete = MsgTemplateKeyword::msgTemplateKeywordDelete($params);
        if ($delete) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }
        return $result;
    }

    /**
     * 添加短信模板
     * @param string $param ['content']        消息内容
     * @param string $param ['prepare_node']   预发节点
     * @param string $param ['msg_type']   消息类型
     * @param string $param ['msg_tag']   消息标签
     * @param string $param ['msg_title']   消息标签
     * @return array
     */
    public function msgTemplateAdd($params)
    {
        $result = ['code' => 10020, 'msg' => "添加失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.msgtemplate.msgtemplate-add'),
            \Config::get('validator.system.msgtemplate.msgtemplate-key'),
            \Config::get('validator.system.msgtemplate.msgtemplate-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        #判断内容是否重复添加
        $hadInfo = MsgTemplate::where(['msg_type'=> $params['msg_type'],'msg_tag'=>$params['msg_tag'],'msg_title'=>$params['msg_title']])->first();
        if ($hadInfo['content'] == $params['content'] && $hadInfo['prepare_node'] == $params['prepare_node']) {
            return ['code' => 10204, 'msg' => "重复添加"];
        }
        #对添加的模板内容的关键字正则验证
        $content = $params['content'];
        $reg = '/\${[^\}]+\}/';
        preg_match_all($reg, $content, $want);
        $array = array();
        foreach ($want[0] as &$v) {
            $res = MsgTemplateKeyword::where('keyword_name', '=', $v)->first();
            if ($res == null) {
                $array[] = $v;
            }
        }
        if (!empty($array)) {
            return ['code' => 10203, 'msg' => "关键字不存在"];
        }
        $addInfo = MsgTemplate::msgTemplateAdd($params);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }
        return $result;
    }

    /**
     * 消息模板的详情(backend)
     * @return mixed
     */
    public function msgTemplateDetail($params)
    {
        if (!isset($params['id']))
            return ['code' => 90002, 'msg' => '请输入消息模板ID'];
        $result['data']['msgTemplateDetail_info'] = MsgTemplate::msgTemplateDetail($params['id']);
        $result['code'] = 1;
        $result['msg'] = "查询成功";
        return $result;
    }

    /**
     * 消息模板的新增(backend)
     * @param $params ['content']  string    模板内容
     * @param $params ['prepare_node']  string    预发节点
     * @param $params ['type']  string    类型
     * @return mixed
     */
    public function msgTemplateEdit($params)
    {
        $result = ['code' => 10023, 'msg' => "更新失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.msgtemplate.msgtemplate-edit'),
            \Config::get('validator.system.msgtemplate.msgtemplate-key'),
            \Config::get('validator.system.msgtemplate.msgtemplate-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        #对添加的模板内容的关键字正则验证
        $content = $params['content'];
        $reg = '/\${[^\}]+\}/';
        preg_match_all($reg, $content, $want);
        $array = array();
        foreach ($want[0] as &$v) {
            $res = MsgTemplateKeyword::where('keyword_name', '=', $v)->first();
            if ($res == null) {
                $array[] = $v;
            }
        }
        if (!empty($array)) {
            return ['code' => 10203, 'msg' => "关键字不存在"];
        }
        $id = $params['id'];
        unset($params['id']);
        unset($params['s']);//清除数组多余的参数
        $editinfo = MsgTemplate::msgTemplateEdit($id, $params);
        if ($editinfo == 1) {
            $result['code'] = 1;
            $result['msg'] = "更新成功";
        }
        return $result;
    }

    /**
     * 查询所有消息模板
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public function msgTemplateList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $msgTemplateList = MsgTemplate::msgTemplateList($params);
        $data['msgTemplate_list'] = $msgTemplateList;
        $data['total'] = MsgTemplate::msgTemplateCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 删除消息模板
     * @param $params ['id'] 消息模板的ID
     * @return array
     */
    public function msgTemplateDelete($params)
    {
        $result = ['code' => 10023, 'msg' => "删除失败"];
        if (!isset($params['id']))
            return ['code' => 90002, 'msg' => '请输入要删除的消息模板ID'];
        $delete = MsgTemplate::msgTemplateDelete($params);
        if ($delete) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }
        return $result;
    }
    /**
     * 查询消息模版
     * @param $tag
     * @param $type
     * @return mixed
     *
     * @author  liyongchuan
     *
     */
    public function msgTemplateFirst($tag)
    {
        try
        {
            $msgTemplate=MsgTemplate::msgTemplateFirst($tag);
            if($msgTemplate){
                $return=['code'=>1,'data'=>$msgTemplate];
            }else{
                $return=['code'=>10025,'msg'=>'查询失败'];
            }
            return $return;
        }catch(\Exception $e){
            return ['code'=>99999,'msg'=>'程序异常'];
        };
    }

}