<?php
/**
 * Created by PhpStorm.
 * User: caohan
 */

namespace Libraries\Service;


class RsaService {
    public $public_key;
    public $private_key;

    public function __construct() {
        $this->public_key = openssl_pkey_get_public(\Config::get('user.public_key'));
        $this->private_key = openssl_pkey_get_private(\Config::get('user.private_key'));
    }

    //私钥加密
    public function priencrypt($data) {
        if (openssl_private_encrypt($data, $crypted, $this->private_key)) {
            return base64_encode($crypted);
        }
        return '';
    }

    //私钥解密
    public function pridecrypt($data) {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->private_key)) {
            return $decrypted;
        }
        return '';
    }

    public function decrypt_rsa($data){
        $encrypted = base64_decode($data);
        $fiveMBs = 50 * 1024 * 1024;
// 		$file = FCPATH. 'jktest_'.time().'.txt';
        $fp = fopen("php://memory",'w+b');
        fwrite($fp, $encrypted);
        fseek($fp, 0);
        $bContent = '';
        while (!feof($fp)) {
            $bContent .= self::privDecryptNB64(fread($fp, 128));
        }
        fclose($fp);
//     	unlink($file);
        return $bContent;
    }

    public function privDecryptNB64($encrypted)
    {
        if(!is_string($encrypted)){
            return null;
        }
        return (openssl_private_decrypt($encrypted, $decrypted, $this->private_key))? $decrypted : null;
    }

    //公钥加密
    public function pubencrypt($data) {
        if (openssl_public_encrypt($data, $encrypted, $this->public_key)) {
            return base64_encode($encrypted);
        }
        return '';
    }
    //公钥解密
    public function pubdecrypt($data) {
        if (openssl_public_decrypt(base64_decode($data), $decrypted, $this->public_key)) {
            return $decrypted;
        }
        return '';
    }
}