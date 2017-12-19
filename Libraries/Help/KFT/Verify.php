<?php

/**
 * Created by PhpStorm.
 * User: iblur
 * Date: 2015-8-8
 * Time: 14:14
 */
namespace Libraries\Help\KFT;

class Verify
{
    /**
     * @var cer证书路径
     */
    private $cer_path;

    /**
     * @var 待校验签名数据
     */
    private $sign_data;

    /**
     * @var 原始未签名数据
     */
    private $src_data;

    /**
     * @param $cer_path cer证书文件路劲
     * @param $sign_data 签名数据
     * @param $src_data 原始未签名数据
     */
    public function __construct($cer_path, $sign_data, $src_data)
    {
        $this->cer_path = $cer_path;
        $this->sign_data = $sign_data;
        $this->src_data = ParamUtil::sort_params($src_data);
    }

    public function get_public_key()
    {
        $x509data = file_get_contents($this->cer_path);
        openssl_x509_read($x509data);
        $cert_data = openssl_x509_parse($x509data);
        $cert_id = $cert_data ['serialNumber'];
        var_dump($cert_id);
    }

    public function data_verify()
    {
        $x509data = file_get_contents($this->cer_path);
        $flag = openssl_verify($this->src_data, $this->sign_data, $x509data);
        if ($flag == 1) {
            echo 'Ok';
        } else if ($flag == 0) {
            echo 'Bad';
        }
    }

}

$cer_path = 'o:/cert/2.cer';

$params = array(
    'service' => 'trade_record_query',
    'version' => '1.0.0-IEST',
    'charset' => 'utf-8',
    'language' => 'zh_CN',
    'callerIp' => '210.79.79.167',
    'merchantId' => '2015072300081421',
    'productNo' => '2GCA0AAH',
    'queryCriteria' => '2ACB0BAB,,14383312930002',
);

$sign_data = "c///SolRSUkOveeM7As5FMZNADsS4brFpgA4+exdPOwKYZImyFeDa7ng1b50awwDUD+CjuyR+FHRERFHoYnDTBj24apGpS2SBp3/sS1/7Vm6RDCYOjZIA+fT1IhopJgSUZMqx2xhIDQFRHkhBd33otw4IzmVhwTdmdI8xKQei4fBAo91cQDM6dJTiri55kpOSokvKIrbDxNRYM3vKtm4U2Kq3J7qasD3jfYam/JP9cj37bU4rSA0ve2EM2k1aYUtog6i/HKWO3zzz4tKX6ZvCFNOLkRgZ561FwsXK9xnbqexagZPk3k/qM/eo2iMYhV1n8chHeSbnw4VCBzu21spGw==";

$verify = new Verify($cer_path, $sign_data, $params);
//$verify->get_public_key();
$verify->data_verify();
$verify->get_publickey();