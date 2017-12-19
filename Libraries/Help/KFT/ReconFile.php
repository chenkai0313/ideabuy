<?php

/**
 * Created by PhpStorm.
 * User: iblur
 * Date: 2015-8-8
 * Time: 15:36
 */
namespace Libraries\Help\KFT;

class ReconFile
{

    /**
     * @var 快付通文件请求地址中获取到的响应数据
     */
    private $response_data;

    /**
     * @var int 非文件内容长度
     */
    private $file_length;

    /**
     * @var array整个文件的字节数组
     */
    private $file_byte;


    /**
     * ReconFile constructor.
     * @param $response_data 快付通文件请求地址中获取到的响应数据
     */
    public function __construct($response_data)
    {
        $this->response_data = $response_data;
        $this->file_byte = KFTUtil::str_to_byte($response_data);
        $this->file_length = KFTUtil::str_to_int($this->file_byte);
    }

    /**
     * 获取文件响应数据中的非文件内容
     * @return string 响应数据中获取到的非文件内容
     */
    public function get_file_desc()
    {
        $file_desc = array($this->file_length);
        for ($i = 0; $i < $this->file_length; ++$i) {
            $file_desc[$i] = $this->file_byte[4 + $i];
        }
        return KFTUtil::byte_to_str($file_desc);
    }

    /**
     * 将队长文件写入到本地，文件类型为zip
     * @param $file_path 文件保存的绝对路径
     */
    public function write_file($file_path)
    {
        $file_content_length = count($this->file_byte) - $this->file_length - 4;
        $file_content = array($file_content_length);
        for ($i = 0; $i < $file_content_length; ++$i) {
            $file_content[$i] = $this->file_byte[$i + $this->file_length + 4];
        }
        $file = fopen($file_path, 'w+');
        $content_str = KFTUtil::byte_to_str($file_content);
        for ($written = 0; $written < strlen($content_str); $written += $fwrite) {
            $fwrite = fwrite($file, substr($content_str, $written));
        }
        fclose($file);
    }


}





