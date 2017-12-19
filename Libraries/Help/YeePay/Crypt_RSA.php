<?php
/**
 * User: zhengchao
 * Date: 2016/8/31
 * Time: 15:25
 */
namespace Libraries\Help\YeePay;
class Crypt_RSA {
    private $key;
    public function loadKey($key)
    {
        $this->key = $key;
    }
    public function encrypt($input){
        openssl_public_encrypt($input,$encrypted,openssl_pkey_get_public($this->key));
        return base64_encode($encrypted);
    }

    public function decrypt($encrypted){
        openssl_private_decrypt(base64_decode($encrypted),$decrypted,openssl_pkey_get_private($this->key));
        return $decrypted;
    }
}