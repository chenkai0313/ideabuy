<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7 0007
 * Time: 下午 17:03
 */
return [
    #版本
    'device'=>[
        ['value'=>'ios','label'=>'ios'],
        ['value'=>'android','label'=>'安卓'],
        ['value'=>'front-end','label'=>'前端'],
    ],
    #更新方式
    'update_mode'=>[
        ['value'=>'1','label'=>'全量更新'],
        ['value'=>'2','label'=>'增量更新'],
    ],
    #更新类型
    'update_type'=>[
        ['value'=>'1','label'=>'客户端'],
        ['value'=>'2','label'=>'前端资源'],
        ['value'=>'3','label'=>'安卓热更新'],
    ],
    #模块
    'module'=>[
        ['value'=>'1','label'=>'全部'],
        ['value'=>'argument','label'=>'协议模块'],
        ['value'=>'bankCard','label'=>'银行卡模块'],
        ['value'=>'broadband','label'=>'宽带模块'],
        ['value'=>'buyPhone','label'=>'买手机模块'],
        ['value'=>'flowRecharge','label'=>'流量模块'],
        ['value'=>'identity','label'=>'身份信息模块'],
        ['value'=>'mealChange','label'=>'套餐更换模块'],
        ['value'=>'order','label'=>'订单模块'],
        ['value'=>'phoneFee','label'=>'话费充值模块'],
        ['value'=>'queryZone','label'=>'查询专区模块'],
        ['value'=>'repayment','label'=>'还款模块'],
        ['value'=>'servePeople','label'=>'便民服务模块'],
    ],
];