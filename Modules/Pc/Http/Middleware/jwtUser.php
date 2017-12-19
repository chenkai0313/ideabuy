<?php

namespace Modules\Pc\HTTP\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class jwtUser
{
    public function handle($request, Closure $next)
    {
        $this->registerJWTConfig();
        #获取当前路由器信息
        $request_info = request()->route()->getAction();
        $arr = explode('@',$request_info['controller']);
        #若非登录页面，则验证JWT
        if($arr['1']!='userLogin' && $arr['1']!='userAdd') {
            #验证登录   JWT
            try {
                $payload = JWTAuth::parseToken()->getPayload();
                $from = $payload->get('from');
                if (!$from=='user' || !$user = JWTAuth::parseToken()->authenticate()) {
                    return ['code' => 11094, 'msg' => '找不到该会员'];
                }
                if(JWTAuth::getToken()!=rewrite_cache()->get($payload->get('platform'))){
                    return ['code'=>11095,'msg' => '您的帐号在其他设备登入,请重新登入···'];
                }
            } catch (Exception $e) {
                if ($e instanceof TokenInvalidException)
                    return ['code' => 11091, 'msg' => '登录信息验证失败'];
                else if ($e instanceof TokenExpiredException) {
                    return ['code' => 11092, 'msg' => '登录信息过期'];
                } else {
                    return ['code' => 11093, 'msg' => '登录验证失败'];
                }
            }
        }
        return $next($request);
    }

    protected function registerJWTConfig()
    {
        \Config::set('jwt.user' , 'Modules\User\Models\User');
        \Config::set('auth.providers.users.table', 'users');
        \Config::set('auth.providers.users.model', \Modules\User\Models\User::class);
        \Config::set('jwt.identifier' , 'user_id');

    }
}
