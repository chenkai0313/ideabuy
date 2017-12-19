<?php

/**
 * Created by PhpStorm.
 * User: iblur
 * Date: 2015-8-8
 * Time: 12:10
 */
namespace Libraries\Help\KFT;
class Sign
{

    /**
     * @var pfx路径
     */
    private $pfx_path;

    /**
     * @var 密码
     */
    private $password;

    /**
     * Sign constructor.
     * @param $pfx_path pfx路径
     * @param $password 密码
     */
    public function __construct($pfx_path, $password)
    {
        $this->pfx_path = $pfx_path;
        $this->password = $password;
    }


    /**
     * 读取pfx证书获取私匙
     * @return mixed 获取到的私匙
     */
    public function get_private_key()
    {
        $certs = array();
        $pfx_file = file_get_contents($this->pfx_path);
        openssl_pkcs12_read($pfx_file, $certs, $this->password);
        return openssl_get_privatekey($certs['pkey']);
    }

    /**
     * @param $params 排序好的待签名参数
     * @return string base64处理后的加密后的数据
     */
    public function sign_data($params)
    {
        $data = KFTUtil::sort_params($params);
        $private_key = $this->get_private_key();
        openssl_sign($data, $signature, $private_key);
        openssl_free_key($private_key);
        return base64_encode($signature);
    }

    /**
     * 发送请求到kft平台
     * @param $params_array 发送的请求参数
     * @param $sign_data 需要组装到请求参数中的签名数据
     * @param $url 请求的链接地址
     * @param $is_file 是否为请求文件
     * @return mixed 响应数据
     */
    public function request_kft($params_array, $sign_data, $url, $is_file)
    {
        $params_array['signatureAlgorithm'] = 'RSA';
        $params_array['signatureInfo'] = $sign_data;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        if ($is_file) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept' => 'application/octet-stream', 
                'Content-Type' => 'application/x-www-form-urlencoded; text/html; charset=utf-8'               
            ));
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept' => 'text/html',
                'Content-Type' => 'application/x-www-form-urlencoded; text/html; charset=utf-8'               
            ));
        }
        //curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        //curl_setopt($curl, CURLOPT_NOPROGRESS, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params_array));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($curl);
        curl_close($curl);
        //print_r($data);
        return $data;
    }

    public function file_size_and_byte($file_str)
    {
        $file_byte = KFTUtil::str_to_byte($file_str);
        $file_size = KFTUtil::byte_to_int($file_byte);
        return array("file_byte" => $file_byte, "file_size" => $file_size);
    }

}
