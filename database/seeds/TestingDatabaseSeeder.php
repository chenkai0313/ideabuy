<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/15
 * Time: 13:12
 */

use Illuminate\Database\Seeder;
use \Illuminate\Database\Eloquent\Model;

class TestingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(AdminsSeeder::class);
        $this->call(EncryptTokenSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(RoleAdminSeeder::class);
        $this->call(SystemBankInfoSeeder::class);
        $this->call(SystemMsgTemplateSeeder::class);
        $this->call(SystemMsgTemplateInfoSeeder::class);
        $this->call(SystemAdDataSeeder::class);
        $this->call(ArticleTableSeeder::class);
        $this->call(ArticleTypeTableSeeder::class);
        $this->call(GoodsAttributeTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SystemSMSTableSeeder::class);
        $this->call(SystemFileTestTableSeeer::class);
        $this->call(UserStatusTableSeeder::class);
        $this->call(UserApplyTableSeeder::class);
        $this->call(OrderInfoTableSeeder::class);
        $this->call(OrderGoodsTableSeeder::class);
        $this->call(UserInfoTableSeeder::class);
        $this->call(RegionTableSeeder::class);
        $this->call(SupplierTableSeeder::class);
        Model::reguard();
    }
}

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'supplier';
        $params = [
            ['1', '17611110000', '供应商管理员', '$10$AXUEyS40bIDouF3mXJ4Ro.ideFJWmu6yeDm2qz/cJ1dUbZM76I2KK', '33', '3302', '330283', '浙江宁波', null , null , null ,'2017-07-25 17:11:30', '2017-08-11 13:05:36', null],
        ];

        $field = ['supplier_id','supplier_mobile','supplier_name','supplier_password','province','city','district','address','login_ip','login_at','remark','created_at','updated_at','deleted_at'];



        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}
class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'admins';
        $params = [
            ['2', 'admin001', 'q魂牵梦萦q23asdf', '1', '$2y$10$xEbgaPmqBTVj5Y3fWbL0tOj8K42cQF53Evox4z7w5pKFGRvx20cb2', '2017-07-28', null, '0', '2017-07-31 16:22:06', '2017-07-31 17:39:38', null],
        ];
        $field = ['admin_id','admin_name','admin_nick','admin_sex','admin_password','admin_birthday','remember_token',
            'is_super', 'created_at','updated_at','deleted_at'];


        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'users';
        $params = [
            ['1', '', '', '$2y$10$UcePVDn9VkTDA7TgUcWkauGIqM56rGl4NTCuJowTMmVuUK8HcQZxm', '', '', '18268256729', '', '0', '0', null,
                null, null, '0', '', '0.0000', null, null, null, 'ios', '1.0.4', '0', '2017-09-01 10:00:22', '2017-09-01 10:02:55', null],
            ['2', '葛宏华', '', '$2y$10$PSR4/2OVCa/xfyDr2bvkUOnfdoS1wN/vn2vErberl9cAiOkyZaayy', '', '330226198903210015', '15105840179', '', '0', '0', null,
            null, null, '500', 'PyzahLbx', '0.0000', null, null, null, 'ios', '1.0.4', '0', '2017-09-01 10:00:22', '2017-09-01 10:02:55', null],
        ];
        $field = ['user_id','real_name','salt','user_password','user_portrait','user_idcard','user_mobile','pay_password',
            'address_id', 'card_id','qq_openid','wx_openid','remember_token','credit_point','credit_code','white_amount','activate_date',
            'first_bill_date','first_pay_date','client_device','client_version','is_black','created_at','updated_at','deleted_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class AdminLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'admin_log';
        $params = [
            ['1', 'admin', '1', '订单管理/订单详情', '122.227.139.210', '测试', '2017-09-13 16:05:26', '1', '无'],
        ];
        $field = ['log_id','admin_name','admin_id','operate_target','operate_ip','operate_content','operate_time','operate_status','remark'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}
class UserInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'user_info';
        $params = [
            ['1', '2', '2', 'qwe', '123', '测试', '123','123', '123','123','1','2017-09-13 16:05:26','2017-09-13 16:05:26'],
        ];
        $field = ['info_id','user_id','user_education','user_profession','user_company','user_income','user_qq','user_email','link_man','link_mobile',
            'link_relation','created_at','updated_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class UserApplyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'user_apply';
        $params = [
            ['1', '1', 'admin', 'qwe', '123','1', '2017-09-13 16:05:26','2017-09-13 16:05:26','123','1']
            // ['2', '2', '葛宏华', '330226198903210015', '1,2,3', '2', '2017-09-29 17:36:34', '2017-09-29 17:36:34', '', '1']
        ];
        $field = ['apply_id','user_id','real_name','user_idcard','id_img','status','updated_at','created_at','reason','apply_type'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class OrderInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'order_info';
        $params = [
            ['1', '2', '1', 'O2017091155517', '1', '1', '5000.0000', '0.0000', '5000.0000', '光光', '33', '3302', '330283', '0', '南部商务区', '15111111111', '2', '1', '0', '尽快发货', '0', '0', '0', null, null, null, null, '2017-08-07 00:00:00', '2017-09-11 16:42:27', null],
        ];
        $field = ['order_id','user_id','admin_id', 'order_sn','pay_id','order_status','goods_amount','freight_amount','order_amount','consignee','province',
            'city','district','street','address','mobile','order_from','loan_product_id','month','order_remark','white_is_pay_off','supplier_id','parent_id','assign_at','send_at','receive_at','apart_at','created_at','updated_at','deleted_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}
class OrderGoodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'order_goods';
        $params = [
            ['13', 'O2017091155517', 'G929652281714031', '0', '0', '测试手机', 'http://www.gehonghua.com/upload/img/1.png', '颜色|配置', '黑色|64G', '', '1000.0000', '0.0000', '5', '5000.0000', ''],
        ];
        $field = ['id','order_sn','goods_key','goods_id','product_id','goods_name','goods_thumb','attr_name','attr_value','goods_sn','product_price',
            'market_price','goods_number','goods_amount','goods_unit'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}
class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'system_region';
        $params = [
            ['1', '33', '0', '浙江省', '1'],
            ['2', '3302', '11', '宁波市', '2'],
            ['3', '330283', '1101', '鄞州区', '3'],
        ];
        $field = ['region_id','region_code','parent_id','region_name','region_level'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class SystemSMSTableSeeder extends Seeder {
    public function run()
    {
        $table = 'system_sms';
        $params = [
            ['1', '15757390796', 1234, '0', '1', date('Y-m-d H:i:s')],
            ['2', '15757390796', 2345, '0', '2', date('Y-m-d H:i:s')],
            ['3', '15105840179', 3456, '0', '6', date('Y-m-d H:i:s')],
            ['4', '15105840179', 1111, '0', '3', date('Y-m-d H:i:s')],
            ['5', '15105840179', 1234, '0', '4', date('Y-m-d H:i:s')],
        ];
        $field = ['sms_id','mobile','code','status','type','created_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class ArticleTableSeeder extends Seeder
{
    public function run()
    {
        $table = 'system_article';
        $params = [
            ['1', '2', '测试文章', '123123', '0', '0', '2017-08-31 13:50:05', '2017-09-12 09:31:56', null],
            ['2', '2', '测试修改', '<h2>测试图片上传</h2>', '0', '0', '2017-09-04 16:34:18', '2017-09-04 16:58:00', null],
        ];
        $field = ['article_id','type_id','article_title','article_content','admin_id','sort','created_at','updated_at','deleted_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}


class ArticleTypeTableSeeder extends Seeder
{
    public function run()
    {
        $table = 'system_article_type';
        $params = [
            ['1', '新闻', '0', '1', '2017-08-31 11:39:14'],
            ['2', '浙江新闻', '1', '2', '2017-08-31 11:57:23'],
            ['3', '宁波新闻', '2', '3', '2017-08-31 13:57:52'],
        ];
        $field = ['type_id','type_name','parent_id','level','created_at'];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}


Class GoodsAttributeTableSeeder extends Seeder {
    public function run()
    {
        $table = 'goods_attribute';
        $params = [
            ['1', '颜色', '0', '2017-09-20 15:08:07', null],
            ['2', '内存', '0', '2017-09-20 15:08:12', null],
            ['3', '版本', '0', '2017-09-20 15:08:31', null],
            ['4', '合约套餐', '0', '2017-09-20 15:09:08', null],
            ['5', '办理号码', '0', '2017-09-20 15:10:09', null],
            ['6', '优惠活动', '0', '2017-09-20 15:11:24', null],
        ];
        $field = [
            'attr_id','attr_name','sort','created_at','deleted_at',
        ];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class UserStatusTableSeeder extends Seeder {
    public function run()
    {
        $table = 'user_status';
        $params = [
            ['1', '2', '0', '0', '0','1','2'],
        ];
        $field = [
            'status_id','user_id','is_linkman','is_idcard','is_idcard_img','is_activate','status'
        ];

        DB::table($table)->insert(sql_batch_str($field, $params));
    }
}

class SystemFileTestTableSeeer extends Seeder {
    public function run()
    {
        $data = [
            ['1', 'ad/2017/08/25/b2d6ce3422561b3c0ed340031b21dfab.jpg', '1', '2', '2017-09-29 17:36:34', null],
            ['2', 'ad/2017/09/13/c09a121bd4cfd8da7b76d1b06e11867d.jpg', '1', '2', '2017-09-29 17:36:34', null],
            ['3', 'ad/2017/08/18/bc205768dbb85f0912caaec62294ee2a.jpg', '1', '2', '2017-09-29 17:36:34', null],

        ];

        $field = ['file_id','file_path','file_type','user_id','created_at','deleted_at'];

        DB::table('system_file')->insert(sql_batch_str($field,$data));
    }
}

