<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/28
 * Time: 10:47
 */
namespace Modules\Supplier\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Entrust;

class jwtSupplier
{
    public function handle($request ,Closure $next)
    {
        $this->registerJWTConfig();
        #获取当前路由器信息
        $request_info = request()->route()->getAction();
        $arr = explode('@',$request_info['controller']);
        #若非登录页面，则验证JWT与RBAC
        if($arr['1']!='supplierLogin'){
            #验证登录   JWT
            try {
                $payload = JWTAuth::parseToken()->getPayload();
                $from = $payload->get('from');
                if (!$from=='supplier' || !$user = JWTAuth::parseToken()->authenticate()) {
                    return ['code' => 30004,'msg' => '找不到该供应商'];
                }
            } catch (Exception $e) {
                if ($e instanceof  TokenInvalidException)
                    return ['code'=>10091,'msg'=>'token信息不合法'];
                else if ($e instanceof TokenExpiredException) {
                    return ['code'=>10092,'msg'=>'登录信息过期'];
                }else{
                    return ['code'=>10093,'msg'=>'登录验证失败'];
                }
            }
            #验证权限   RBAC
//            if(!Entrust::user()->is_super){//判断是否超级管理员
//                if(!Entrust::can($arr['1'])){
//                    return ['code'=>10015,'msg'=>'无权操作'];
//                }
//            }
        }
        return $next($request);
    }
    /**
     * 重写JWT config默认参数
     */
    protected function registerJWTConfig()
    {
        \Config::set('jwt.user' , 'Modules\Supplier\Models\Supplier');
        \Config::set('jwt.identifier' , 'supplier_id');
        \Config::set('auth.providers.users.table', 'supplier');
        \Config::set('auth.providers.users.model', \Modules\Supplier\Models\Supplier::class);
        //\Config::set('cache.default','array');//RBAC
    }
}