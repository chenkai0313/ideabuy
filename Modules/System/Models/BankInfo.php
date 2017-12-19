<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/3
 * Time: 17:36
 */
namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class BankInfo extends Model
{
    #表名
    protected $table = 'system_bank_info';
    #主键
    protected $primaryKey = 'bank_id';

    /**
     * 获取bank_id
     * @param $bankId
     * @return mixed
     */
    public static function  bankInfoGet($bankId)
    {
        return BankInfo::where('bank_code',$bankId)->value('bank_id');
    }

    /**
     * 获取全部银行名称
     *
     * @return \Illuminate\Support\Collection
     */
    public static function bankNameList()
    {
        return BankInfo::select('bank_name','bank_logo')->get();
    }

    /**
     * 获取bankInfo详情
     *
     * @param $bank_id
     * @return mixed
     *
     * @author  liyongchuan
     * @time 2017-09-22
     */
    public static function BankInfoDetail($bank_id)
    {
        return BankInfo::where('bank_id',$bank_id)->first();
    }
}