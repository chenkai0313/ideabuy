<?php
namespace Modules\Api\HTTP\Middleware;


use Closure;

class rsaMid
{
    public function handle($request,Closure $next)
    {
        #方法1 加密方式是 所有参数加密成一个 加密字符串
        $rsa_params = $request->input('params');//获取加密后的参数
        try {
            if (!is_null($rsa_params)) {
                $params = \Rsa::decrypt_rsa($rsa_params);;//私钥解密
                $params = json_decode($params, true);//解析
                $request->merge($params);//把数组加入到request中
                return $next($request);
            }
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => '解析出错啦']);
        }

        #方法2 加密方式是 key里面的字符串加密
//        $params = $request->input();
//        foreach ($params as $key => $value)
//            if ($key != 's')
//                $params[$key] = substr(\Rsa::pridecrypt($value),1,strlen(\Rsa::pridecrypt($value))-2);//需要把前后两端的双引号去掉
//
//        $request->merge($params);//把数组加入到request中

//        #方法3 在payload里解析
//        try {
//            $rsa_params = $request->getContent();
//            $params = \Rsa::pridecrypt($rsa_params);//私钥解密
//            $params = json_decode($params, true);//解析
//        } catch (\Exception $e) {
//            return json_encode(['code' => 500, 'msg' => '解析出错啦']);
//        }

//        $request->merge($params);
        return $next($request);
    }


}