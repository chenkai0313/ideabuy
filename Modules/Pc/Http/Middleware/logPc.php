<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 11:38
 */

namespace Modules\Pc\HTTP\Middleware;

use Closure;

class logPc
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $params = $request->header();
        array_push($params, $request->all());
        \Log::useFiles(storage_path() . '/logs/pc-' . date('Y-m-d') . '-info.log', 'info');
        \Log::info('客户端请求log：', $params);
        \Log::info('服务端返回log' . $response);
        return $response;
    }
}