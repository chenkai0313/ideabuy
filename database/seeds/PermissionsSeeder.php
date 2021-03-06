<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', 'index', '仪表盘', '一级导航', '0', '1', '/dashboard', '1', '2017-07-27 15:59:00', '2017-08-02 15:17:43'],
            ['2', 'contentManage', '内容管理', '一级导航', '0', '1', '/content', '1', '2017-07-27 15:59:00', '2017-08-12 10:40:33'],
            ['3', 'adType', '广告分类', '控制器', '2', '2', '/content/ad-type', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['4', 'adTypeList', '广告分类列表', '方法', '3', '3', '', '1', '2017-07-27 15:59:00', '2017-09-12 10:20:43'],
            ['5', 'adTypeAdd', '广告分类添加', '方法', '3', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['6', 'adTypeEdit', '广告分类编辑', '方法', '3', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['7', 'adTypeDelete', '广告分类删除', '方法', '3', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['8', 'adTypeDetail', '广告分类详细', '方法', '3', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['9', 'ad', '广告管理', '控制器', '2', '2', '/content/ad-list', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['10', 'adAdd', '广告添加', '方法', '9', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['11', 'adEdit', '广告编辑', '方法', '9', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['12', 'adDelete', '广告删除', '方法', '9', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['13', 'adDetail', '广告详细', '方法', '9', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['14', 'adList', '广告列表', '方法', '9', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['15', 'admin', '用户管理', '控制器', '33', '2', '/setting/users', '1', '2017-07-27 15:59:00', '2017-08-01 17:00:17'],
            ['16', 'adminList', '管理员列表', '方法', '15', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['17', 'adminAdd', '管理员添加', '方法', '15', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['18', 'adminEdit', '管理员编辑', '方法', '15', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['19', 'adminDelete', '管理员删除', '方法', '15', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['20', 'adminDetail', '管理员详细', '方法', '15', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['21', 'articleType', '文章分类', '控制器', '2', '2', '/content/article-type', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['22', 'articleTypeAdd', '文章分类添加', '方法', '21', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['23', 'articleTypeEdit', '文章分类编辑', '方法', '21', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['24', 'articleTypeDelete', '文章分类删除', '方法', '21', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['25', 'articleTypeList', '文章分类列表', '方法', '21', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['26', 'articleTypeDetail', '文章分类详细', '方法', '21', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['27', 'article', '文章管理', '控制器', '2', '2', '/content/article-list', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['28', 'articleAdd', '文章添加', '方法', '27', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['29', 'articleEdit', '文章编辑', '方法', '27', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['30', 'articleDelete', '文章删除', '方法', '27', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['31', 'articleList', '文章列表', '方法', '27', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['32', 'articleDetail', '文章详细', '方法', '27', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['33', 'system', '系统设置', '一级导航', '0', '1', '/setting', '1', '2017-07-27 15:59:00', '2017-08-02 15:18:14'],
            ['34', 'permission', '权限管理', '控制器(测试)', '33', '2', '/setting/permission', '1', '2017-07-27 15:59:00', '2017-08-01 17:00:43'],
            ['35', 'permissionAdd', '权限添加', '方法', '34', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['36', 'permissionEdit', '权限编辑', '方法', '34', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['37', 'permissionList', '权限列表', '方法', '34', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['38', 'permissionDetail', '权限详细', '方法', '34', '3', '', '1', '2017-07-27 15:59:00', '2017-07-27 15:59:00'],
            ['39', 'role', '角色管理', '控制器(测试)', '33', '2', '/setting/role', '1', '2017-07-27 15:59:00', '2017-08-23 19:58:07'],
            ['40', 'roleAdd', '角色添加', '方法', '39', '3', '', '1', '2017-07-27 15:59:00', '2017-08-23 19:58:23'],
            ['41', 'roleEdit', '角色编辑', '方法', '39', '3', '', '1', '2017-07-27 15:59:00', '2017-08-23 19:58:33'],
            ['42', 'roleDelete', '角色删除', '方法', '39', '3', '', '1', '2017-07-27 15:59:00', '2017-08-23 19:58:44'],
            ['43', 'roleList', '角色列表', '方法', '39', '3', '', '1', '2017-07-27 15:59:00', '2017-08-23 19:58:56'],
            ['44', 'roleDetail', '角色详细', '方法', '39', '3', '', '1', '2017-07-27 15:59:00', '2017-08-23 19:59:07'],
            ['45', 'permissionRoleList', '角色权限列表', '方法', '34', '3', '', '1', '2017-07-31 16:33:26', '2017-07-31 16:33:26'],
            ['46', 'permissionRoleAdd', '角色权限添加', '方法', '34', '3', '', '1', '2017-07-31 16:35:23', '2017-07-31 16:35:23'],
            ['47', 'permissionRoleDetail', '角色权限详细', '方法', '34', '3', '', '1', '2017-08-01 10:09:28', '2017-08-01 10:09:38'],
            ['48', 'adTypeSpinner', '广告分类下拉框', '方法', '9', '3', '', '1', '2017-08-01 16:02:15', '2017-08-01 16:02:21'],
            ['49', 'articleTypeSelect', '文章分类下拉框', '方法', '21', '3', '', '1', '2017-08-01 16:05:35', '2017-08-01 16:05:41'],
            ['50', 'permissionType', '权限下拉框', '方法', '34', '3', '', '1', '2017-08-01 16:44:30', '2017-08-01 16:44:38'],
            ['52', 'logManage', '日志管理', '一级导航', '0', '1', '/log', '1', '2017-08-22 09:51:42', '2017-08-22 09:51:44'],
            ['53', 'orderManage', '订单管理', '一级导航', '0', '1', '/order', '1', '2017-08-22 09:50:05', '2017-08-22 17:46:14'],
            ['54', 'messageManage', '消息管理', '一级导航', '0', '1', '/message', '1', '2017-08-22 09:39:39', '2017-08-22 09:39:42'],
            ['55', 'msgTemplate', '消息模板管理', '控制器', '54', '2', '/message/msg-tpl-list', '1', '2017-08-21 11:12:18', '2017-08-21 11:12:18'],
            ['56', 'msgTemplateAdd', '消息模板添加', '方法', '55', '3', '', '1', '2017-08-21 11:15:56', '2017-08-21 11:15:56'],
            ['57', 'msgTemplateEdit', '消息模板编辑', '方法', '55', '3', '', '1', '2017-08-21 11:16:19', '2017-08-21 11:16:19'],
            ['58', 'msgTemplateList', '消息模板列表', '方法', '55', '3', '', '1', '2017-08-21 11:16:41', '2017-08-21 11:16:41'],
            ['59', 'msgTemplateDetail', '消息模板详情', '方法', '55', '3', '', '1', '2017-08-21 11:17:02', '2017-08-21 11:17:02'],
            ['60', 'msgTemplateKeyword', '消息模板关键字', '控制器', '54', '2', '/message/msg-tpl-key', '1', '2017-08-21 11:18:53', '2017-08-21 11:18:53'],
            ['61', 'msgTemplateKeywordAdd', '消息模板关键字添加', '方法', '60', '3', '', '1', '2017-08-21 11:19:32', '2017-08-21 11:19:32'],
            ['62', 'msgTemplateKeywordEdit', '消息模板关键字编辑', '方法', '60', '3', '', '1', '2017-08-21 11:20:41', '2017-08-21 11:20:41'],
            ['63', 'msgTemplateKeywordList', '消息模板关键字列表', '方法', '60', '3', '', '1', '2017-08-21 11:21:02', '2017-08-21 11:21:02'],
            ['64', 'msgTemplateKeywordDetail', '消息模板关键字详情', '方法', '60', '3', '', '1', '2017-08-21 11:21:24', '2017-08-21 11:21:24'],
            ['65', 'order', '订单管理', '控制器', '53', '2', '/order/order-list', '1', '2017-08-21 11:40:24', '2017-08-21 11:40:24'],
            ['66', 'orderList', '订单列表', '方法', '65', '3', '', '1', '2017-08-21 11:42:18', '2017-08-21 11:42:18'],
            ['67', 'orderDetail', '订单详情', '方法', '65', '3', '', '1', '2017-08-21 11:42:35', '2017-08-21 11:42:35'],
            ['68', 'userManage', '会员管理', '一级导航', '0', '1', '/member', '1', '2017-08-21 13:35:15', '2017-08-21 13:35:15'],
            ['69', 'user', '会员管理', '控制器', '68', '2', '/member/member-list', '1', '2017-08-21 19:32:07', '2017-08-21 19:32:07'],
            ['70', 'userList', '会员列表', '方法', '69', '3', '', '1', '2017-08-21 19:33:16', '2017-08-21 19:33:16'],
            ['71', 'userInfoDetail', '会员详情', '方法', '69', '3', '', '1', '2017-08-21 19:33:37', '2017-08-21 19:33:37'],
            ['72', 'userApplyReviewList', '会员审核列表', '方法', '77', '3', '', '1', '2017-08-21 19:34:18', '2017-08-21 19:34:18'],
            ['73', 'userApplyReview', '会员审核详情', '方法', '77', '3', '', '1', '2017-08-21 19:34:45', '2017-08-21 19:34:45'],
            ['74', 'userReviewOperatio', '会员审核操作', '方法', '77', '3', '', '1', '2017-08-21 19:35:22', '2017-08-21 19:35:22'],
            ['75', 'log', '操作日志', '控制器', '52', '2', '/log/operation-log', '1', '2017-08-21 20:10:54', '2017-08-21 20:10:54'],
            ['76', 'logList', '操作日志列表', '方法', '75', '3', '', '1', '2017-08-21 20:11:37', '2017-08-21 20:11:37'],
            ['77', 'userApply', '会员审核', '控制器', '68', '2', '/member/member-verify', '1', '2017-08-23 10:54:57', '2017-08-23 10:54:57'],
            ['78', 'roleListAll', '所有角色列表', '方法', '39', '3', '/member/member-verify', '1', '2017-08-23 10:54:57', '2017-08-23 10:54:57'],
            ['80', 'messageAnnounce', '公告管理', '控制器', '54', '2', '/message/msg-list', '1', '2017-09-01 15:30:04', '2017-09-01 15:30:04'],
            ['81', 'messageNotice', '通知管理', '控制器', '54', '2', '/message/notice-list', '1', '2017-09-01 15:48:19', '2017-09-01 15:48:19'],
            ['82', 'messagePush', '推送管理', '控制器', '54', '2', '/message/push-list', '1', '2017-09-01 15:57:42', '2017-09-01 15:57:42'],
            ['83', 'messageSms', '短信管理', '控制器', '54', '2', '/message/sms-list', '1', '2017-09-01 15:58:36', '2017-09-01 15:58:36'],
            ['84', 'messageSend', '消息公告发布', '控制器', '54', '2', '/message/mass-msg', '1', '2017-09-01 16:01:34', '2017-09-01 16:01:34'],
            ['85', 'announceList', '公告列表', '方法', '80', '3', '', '1', '2017-09-01 16:07:53', '2017-09-01 16:07:53'],
            ['86', 'noticeList', '通知列表', '方法', '81', '3', '', '1', '2017-09-01 16:08:19', '2017-09-01 16:08:19'],
            ['87', 'pushList', '推送列表', '方法', '82', '3', '', '1', '2017-09-01 16:09:31', '2017-09-01 16:09:31'],
            ['88', 'smsList', '短信列表', '方法', '83', '3', '', '1', '2017-09-01 16:10:34', '2017-09-01 16:10:34'],
            ['89', 'pushSelect', '发布公告参数', '方法', '84', '3', '', '1', '2017-09-01 16:11:28', '2017-09-01 16:11:28'],
            ['90', 'orderAdd', '订单添加', '控制器', '53', '2', '/order/add-order', '1', '2017-09-06 13:20:14', '2017-09-06 13:20:14'],
            ['91', 'orderAddOperate', '添加订单', '方法', '90', '3', '', '1', '2017-09-06 13:21:04', '2017-09-06 13:21:04'],
            ['92', 'version', '版本管理', '控制器', '33', '2', '/setting/version', '1', '2017-09-08 10:18:39', '2017-09-08 10:18:43'],
            ['93', 'versionList', '版本列表', '方法', '92', '3', '', '1', '2017-09-08 10:18:54', '2017-09-08 10:18:56'],
            ['94', 'versionDelete', '版本删除', '方法', '92', '3', '', '1', '2017-09-08 10:20:22', '2017-09-08 10:20:25'],
            ['95', 'versionAdd', '版本新增', '方法', '92', '3', '', '1', '2017-09-08 10:21:07', '2017-09-08 10:21:09'],
            ['96', 'versionAddDisplay', '版本新增显示', '方法', '92', '3', '', '1', '2017-09-08 10:21:42', '2017-09-08 10:21:44'],
            ['97', 'permissionLeft', '左侧菜单', '方法', '34', '3', '', '1', '2017-09-21 11:57:22', '2017-09-21 11:57:22'],
            ['98', 'orderApart', '订单拆分', '方法', '65', '3', '', '1', '2017-09-21 11:57:22', '2017-09-21 11:57:22'],
            ['105', 'goodsManage', '商品管理', '一级导航', '0', '1', '/goods', '1', '2017-09-25 15:14:15', '2017-09-25 15:14:25'],
            ['106', 'goodsBrand', '商品品牌', '', '105', '2', '/goods/goods-brand', '1', '2017-09-25 15:32:57', '2017-09-25 15:32:57'],
            ['107', 'goodsCategory', '商品分类', '', '105', '2', '/goods/goods-category', '1', '2017-09-25 15:38:31', '2017-09-25 15:38:31'],
            ['108', 'goods', '商品列表', '控制器', '105', '2', '/goods/goods-list', '1', '2017-09-25 15:39:03', '2017-09-25 15:39:03'],
            ['109', 'goodsList', '商品列表', '方法', '108', '3', '', '1', '2017-09-26 13:41:18', '2017-09-26 13:41:18'],
            ['110', 'goodsDetail', '商品详情', '方法', '108', '3', '', '1', '2017-09-26 13:46:07', '2017-09-26 13:46:07'],
            ['111', 'goodsAdd', '商品添加', '方法', '108', '3', '', '1', '2017-09-26 13:46:18', '2017-09-26 13:46:18'],
            ['112', 'goodsEdit', '商品修改', '方法', '108', '3', '', '1', '2017-09-26 13:46:29', '2017-09-26 13:46:29'],
            ['113', 'goodsDelete', '商品删除', '方法', '108', '3', '', '1', '2017-09-26 13:46:45', '2017-09-26 13:46:45'],
            ['114', 'goodsStatusChange', '商品状态批量修改', '方法', '108', '3', '', '1', '2017-09-26 13:47:16', '2017-09-26 13:47:16'],
            ['115', 'goodsSelect', '商品选择信息', '方法', '108', '3', '', '1', '2017-09-26 13:48:15', '2017-09-26 13:48:15'],
            ['116', 'goodsProductList', '货品列表', '方法', '108', '3', '', '1', '2017-09-26 14:06:20', '2017-09-26 14:06:20'],
            ['117', 'goodsProductAdd', '货品添加', '方法', '108', '3', '', '1', '2017-09-26 14:06:32', '2017-09-26 14:06:32'],
            ['118', 'goodsProductEdit', '货品修改', '方法', '108', '3', '', '1', '2017-09-26 14:06:41', '2017-09-26 14:06:41'],
            ['119', 'goodsProductDelete', '货品删除', '方法', '108', '3', '', '1', '2017-09-26 14:06:49', '2017-09-26 14:06:49'],
            ['120', 'goodsProductStatusChange', '货品状态批量修改', '方法', '108', '3', '', '1', '2017-09-26 14:07:07', '2017-09-26 14:07:07'],
            ['121', 'attributeAdd', '商品属性添加', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['122', 'attributeEdit', '商品属性编辑', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['123', 'attributeDel', '商品属性删除', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['124', 'attributeList', '商品属性列表', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['125', 'typeAdd', '类型添加', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['126', 'typeEdit', '类型编辑', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['127', 'typeDel', '类型删除', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['128', 'typeList', '类型列表', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
            ['129', 'typeAllList', '所有类型和属性列表', '方法', '108', '3', '', '0', '2017-09-26 15:22:40', '2017-09-26 15:22:40'],
        ];
        $field = ['id','name','display_name','description','pid','level','path','show','created_at','updated_at'];
        DB::table('permissions')->insert(sql_batch_str($field,$data));
    }
}
