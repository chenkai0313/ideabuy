<?php

return [
    'name' => 'User',
    'riskcontrol_url' => env('RISK_DOMAIN','http://r.d.weknet.cn'),//风控url
    'riskcontrol_url_credit' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/credit-calculation',//返回信用的完整url
    'riskcontrol_url_install_getinstalltypeplan' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/install-getinstalltypeplan',//获取分期类型详情 3期 6期 ....
    'riskcontrol_url_account_list' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-month-account-list',//获取分期List  账单首页
    'riskcontrol_url_install_info' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/contract-install-intro',//获取分期所有信息
    'riskcontrol_url_immediate_repayment' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-result-add',//立即还款
    'riskcontrol_url_overdue_list' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/loan-overdue-list',//逾期明细
    'riskcontrol_url_all_bill' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-account-list',//全部账单
    'riskcontrol_url_account_index' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-account-index',//白条首页

    'pc_ideabuy_url' => env('pc_ideabuy_url','http://www.baidu.com'),

    'search_keyword' => '分期购机',//首页关键词

    'avatar_spec'=>"?x-oss-process=image/resize,w_100",

    'public_key' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2lKZQbKOtHlFLnxche+sk6TUD
1lsMaCHuqbm0nFglN5RuZJBtpbmIKskqwrveYkzsL21FW2c7fhhvaMX7te59f+gk
ukLo6Ltd4bCYXnXQlXJU8+J0ybBdLdsMP0qbtfQxXANlWoR0u403YTh242UcjvcD
Hw7wLFOTgSz70Z8ZqwIDAQAB
-----END PUBLIC KEY-----
',

    'private_key'=> '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC2lKZQbKOtHlFLnxche+sk6TUD1lsMaCHuqbm0nFglN5RuZJBt
pbmIKskqwrveYkzsL21FW2c7fhhvaMX7te59f+gkukLo6Ltd4bCYXnXQlXJU8+J0
ybBdLdsMP0qbtfQxXANlWoR0u403YTh242UcjvcDHw7wLFOTgSz70Z8ZqwIDAQAB
AoGAF4p/DyuSxwV7seZnUxfpL68p+L6wqp7akS0tFo56BwJtjvZEcP7HNzldCrhl
BL1j8agDzMeGPfN6LxMxQ80nXKsmwDE40AensQDTiATGEINjnRQkwpK3jlDxUtcJ
B4VTyX0ZSTgPoaNTNvdCFUO8bf+EdxEz14Bqqx/qr1INEnECQQDrfnnObkxX/o1B
Eva5CckmC4Rtv6V1oP3XXOgkDaBt09ofUozvkHrK7gqbhlmu2oAVn8dyApMTzEtt
SIjVPyWvAkEAxnqnOdeabjVu4JWbk+iRDjVrCkqYz8jP9nc9e8PIAqYKvlVnIYz6
wl1Nib0gPYSuaSYTVPsiX+Z8KznYk2sGxQJBAJqFy/FLaKbYreFES0ZRiH6BUi0d
crmDoOy+1shJdLp8J4UkCrxrZldf6O/yMUjNsPv/csR5sf5ssBWOYdLjc5sCQBLH
njUJ4nHVGnWjkkHvenImuccSgdz/OjYu2CFyx+UAQvn5aLWy+jMT0vdabseDW2FV
mfSrgngifdM1OPnR4aUCQCVZAeqeenSPbpkljj0pTGJ/6LM30HC3PIYn8RL1Kxbo
BHB2BLzWpJJLS4FjB1UPFaZ6mZmvJYyA63EzXLZPFH4=
-----END RSA PRIVATE KEY-----',

    'function_open' => env('FUNC_OPEN',false),
];
