<?php
/**
 * Created by PhpStorm.
 * User: 吕成
 * Date: 2017/7/31
 * 用户登录等操作 逻辑层
 */

namespace Modules\User\Services;

use App\Events\NotifyPosh;
use function GuzzleHttp\Psr7\_caseless_remove;
use Modules\System\Services\SmsPushService;
use Modules\User\Models\User;
use Modules\User\Models\UserInfo;
use Modules\User\Models\UserAddress;
use Modules\User\Models\UserCard;
use Modules\User\Models\UserApply;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\UserStatus;

class BackendUserService
{
    /**
     * 后台添加用户
     * @param $params
     * @return array
     */
    public function userAddBackend($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-add-backend'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $params['user_password'] = bcrypt($params['user_password']);//加密
        $user = User::userAdd($params);
        if ($user) {
            $result['code'] = 1;
            $result['msg'] = '新增成功';
        } else {
            $result['code'] = 10070;
            $result['msg'] = '新增失败';
        }
        return $result;
    }

    /**
     * 用户列表(backend)
     * @param $params
     * @return array
     */
    public function userList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['user_list'] = User::userList($params);
        foreach ($data['user_list'] as $k => $v) {
            //获取用户基本信息
            $userBasicInfo = UserInfo::where('user_id', $v['user_id'])->first();
            if (empty($userBasicInfo)) {
                #给前端添加唯一标识，如果基本信息为空，返回为0；
                $data['user_list'][$k]['is_BasicInfo'] = '0';
            } else {
                $data['user_list'][$k]['is_BasicInfo'] = '1';
            }
        }
        $data['total'] = User::userCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 用户审核列表(backend)
     * @param $params
     * @return array
     */
    public function userApplyReviewList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['userapply_list'] = UserApply::userApplyReviewList($params);
        $data['total'] = UserApply::userApplyCount($params);
        return ['code' => 1, 'data' => $data];

    }

    /**
     * 用户详情(backend)
     * @param $params
     * @return array
     */
    public function userInfoDetail($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        if (!isset($params['user_id']) || $params['user_id'] <= 0) {
            return ['code' => 90001, 'msg' => '用户详情id参数错误'];
        }

        //获取用户基本信息
        $userBasicInfo = UserInfo::UserBasicInfo($params);
        if (empty($userBasicInfo)) {
            return ['code' => 90001, 'msg' => '用户基本信息未填写'];
        }
        //获取用户地址信息
        $userAddressInfo = UserAddress::find($userBasicInfo['address_id']);
        $result['data']['user_address'] = [];
        if ($userAddressInfo || !isset($userBasicInfo['address_id']) || $userBasicInfo['address_id'] <= 0) {
            // return ['code' => 90001, 'msg' => '用户地址信息未填写'];
        } else {
            if ($userAddressInfo['province'] != '' && $userAddressInfo['city'] != '' && $userAddressInfo['district'] != '') {
                $area = [
                    $userAddressInfo['province'],
                    $userAddressInfo['city'],
                    $userAddressInfo['district'],
                ];

                $region = \RegionService::regionGet($area);
                $result['data']['user_address']['user_province'] = $region['data']['province'];
                $result['data']['user_address']['user_city'] = $region['data']['city'];
                $result['data']['user_address']['user_district'] = $region['data']['district'];
                $result['data']['user_address']['user_address'] = $userAddressInfo['user_address'];
            }
        }

        //获取用户银行卡信息
        $userCardInfo = UserCard::userCardList($params);

        foreach ($userCardInfo as $key => $vo) {
            if (!empty($vo['bank_logo'])) {
                $userCardInfo[$key]['bank_logo'] = \Config::get('services.oss.host') . '/' . $vo['bank_logo'];
            }
            $userCardInfo[$key]['card_number'] = substr($vo['card_number'], -4);
        }
        if (empty($userCardInfo)) {
            // return ['code' => 90001, 'msg' => '用户银行卡信息未填写'];
        }
        //获取身份证照片信息
        $userApplyInfo = UserApply::userApplyInfo($params);

        $id_img = $userApplyInfo['id_img'];
        $id_img = explode(',', $id_img);
        $userIdPhoto = \FileService::UserIdPhoto($id_img);
        if (empty($userIdPhoto)) {
            // return ['code' => 90001, 'msg' => '用户身份证信息未上传'];
        }
        $result['data']['user_info'] = $userBasicInfo;
        $result['data']['card_info'] = $userCardInfo;
        $result['data']['user_idphoto'] = $userIdPhoto;
        $result['code'] = 1;
        return $result;
    }

    /**
     * 用户申请审核详情(backend)
     * @param $params
     * @return array
     */

    public function userApplyReview($params)
    {
        if (!isset($params['user_id']) || $params['user_id'] <= 0) {
            return ['code' => 90001, 'msg' => '用户申请ID参数错误'];
        }
        //获取用户申请信息
        $userApplyInfo = UserApply::userApplyInfo($params)->toArray();
        $id_img = $userApplyInfo['id_img'];
        $id_img = explode(',', $id_img);
        $userIdPhoto = \FileService::UserIdPhoto($id_img);
        //获取身份证照片
        $result['data']['apply_info'] = $userApplyInfo;
        $result['data']['user_idphoto'] = $userIdPhoto;
        $result['code'] = 1;
        return $result;
    }

    /**
     * 用户审核操作(backend)
     * @param $params
     * @return array
     */

    public function userReviewOperatio($params)
    {
        if (!isset($params['user_id']) || $params['user_id'] <= 0) {
            return ['code' => 90001, 'msg' => '用户申请ID参数错误'];
        }
        if (!(isset($params['status']) && in_array($params['status'], array(1, 2, 3)))) {
            return ['code' => 10071, 'msg' => '审核错误'];
        }
        $params['reason'] = isset($params['reason']) ? $params['reason'] : '';
        $res2 = UserApply::userApplyInfo($params);
        switch ($res2['status']) {
            case 1:
                break;
            case 2:
                return ['code' => 10072, 'msg' => '已经审核通过'];
                break;
            case 3:
                return ['code' => 10073, 'msg' => '审核不通过,请查看理由'];
                break;
            default:
                break;
        }
        DB::beginTransaction();
        //更新用户审核表
        $res1 = UserApply::userApplyUpdate($params);
        //判断是否审核通过
        if ($params['status'] == 2) {
            $params3['user_id'] = $params['user_id'];
            $params3['real_name'] = $res2['real_name'];
            $params3['user_idcard'] = $res2['user_idcard'];
            $params3['activate_date'] = date('Y-m-d', time());
            $params4['user_id'] = $params['user_id'];
            $params4['status'] = $params['status'];
            $res3 = User::userEdit($params3);
            $res4 = UserStatus::userStatusUpdate($params4);
            $res5 = \UserService::userActiveWhite($params);
        } else {
            $res3 = $res4 = $res5 = true;
        }
        if ($params['status'] == 3) {
            $params4['user_id'] = $params['user_id'];
            $params4['status'] = $params['status'];
            $params4['is_idcard'] = 0;
            $params4['is_idcard_img'] = 0;
            $res4 = UserStatus::userStatusUpdate($params4);
        }
        if ($res1 != false && $res3 && $res4 && $res5) {
            DB::commit();
            $result['code'] = 1;
            $result['msg'] = '审核成功';

            $title = '会员审核';
            if ($params['status'] == 3) {
                $title .= '-审核不通过';
                $description = '不好意思,审核失败：' . $params['reason'];
                $status = 0;
            } else {
                $title .= '-审核通过';
                $description = '恭喜、审核通过';
                $status = 1;
            }
            $push = [
                'operate_type' => '3',
                'user_id' => $params['user_id'],
                'audience' => 'regis_id',
                'title' => $title,
                'description' => $description,
                'result' => json_encode(['code' => $status, 'msg' => $description]),
                'message_type' => 'user_apply',
                'type' => 2, // 1公告 2通知
            ];
            \MessageService::messageEntry($push);
//            $new_push=[
//                'title' => $title,
//                'content' => $description,
//                'result'=>['code'=>$status,'msg' => $description],
//                'target_id'=>[$params['user_id']],
//                'message_type' => 1,
//                'operate_type' => '3',
//                'send_type'=>2,
//            ];
//            \Event::fire(new NotifyPosh($new_push));
        } else {
            DB::rollback();
            $result['code'] = 10074;
            $result['msg'] = '审核失败';
        }

        return $result;

    }

}