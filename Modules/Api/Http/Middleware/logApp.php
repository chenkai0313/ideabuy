<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16 0016
 * Time: 上午 10:42
 */
namespace Modules\Api\HTTP\Middleware;


use Closure;

class logApp
{
    public function handle($request,Closure $next)
    {
        $response = $next($request);
        $params=$request->header();
        array_push($params,$request->all());
        \Log::useFiles(storage_path().'/logs/api-'.date('Y-m-d').'-info.log','info');
        \Log::info('客户端请求log：',$params);
        \Log::info('服务端返回log'.$response);
        return $response;
    }
}