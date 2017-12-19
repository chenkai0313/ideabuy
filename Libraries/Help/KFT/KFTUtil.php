<?php

/**
 * Created by PhpStorm.
 * User: iblur
 * Date: 2015-8-8
 * Time: 14:37
 */
namespace Libraries\Help\KFT;
class KFTUtil
{

    /**
     * 对需要签名的参数进行排序
     * @param $params 待排序的参数
     * @return string 排序后的参数
     */
    public static function sort_params($params)
    {
        $result = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $result .= sprintf('%s=%s&', $k, $v);
        }
        return substr($result, 0, strlen($result) - 1);
    }

    /**
     * 将从对账文件接口中获取到的数据转成直接数组
     * @param $file_str 对账文件接收到的数据
     * @return array 对账文件转成字节数组
     */
    public static function str_to_byte($file_str)
    {
        $file_byte = array();
        for ($i = 0, $len = strlen($file_str); $i < $len; ++$i) {
            if (ord($file_str[$i]) > 127) {
                $file_byte[$i] = ord($file_str[$i]) - 256;
                continue;
            }
            $file_byte[$i] = ord($file_str[$i]);
        }
        return $file_byte;
    }

    /**
     * 获取对账文件中非文件内容的长度
     * @param $file_byte 对账文件的直接数组
     * @return int 获取到的对账文件大小
     */
    public static function str_to_int($file_byte)
    {
        $mask = 0xff;
        $n = 0;
        for ($i = 0; $i < 4; ++$i) {
            $n <<= 8;
            $temp = $file_byte[$i] & $mask;
            $n |= $temp;
        }
        return $n;
    }

    /**
     * 非文件内容
     * @param $file_byte 字节数组
     * @return string 字节数组转成的字符串
     */
    public static function byte_to_str($file_byte)
    {
        $str = '';
        foreach ($file_byte as $c) {
            $str .= chr($c);
        }
        return $str;
    }

}