<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29 0029
 * Time: 下午 17:37
 */
namespace Libraries\Help\YeePay;
define('YEEPAY_MOBILE_API', 1);
class yeepayMPay {

// 相关配置参数
    protected $account;
    protected $merchantPublicKey;
    protected $merchantPrivateKey;
    protected $yeepayPublicKey;
    protected $cipher = "rijndael-128";
    protected $mode = "ecb";
    // 请求AES密钥
    private $AESKey;
    // CURL 请求相关参数
    public $useragent = 'ePay v2.0';
    public $connecttimeout = 30;
    public $timeout = 30;
    public $ssl_verifypeer = FALSE;
    // CURL 请求状态相关数据
    public $http_header = array();
    public $http_code;
    public $http_info;
    public $url;
    // 请求加密/解密相关算法工具
    private $RSA;
    private $AES;


    //private $API_Mobile_Pay_Url = 'http://pay.dajiekj.com:1443/quickpay/';  //线上地址
    private $API_Mobile_Pay_Url = 'http://123.58.32.43:3580/quickpay/';  //测试地址

    /**
     * - $account 商户账号
     * - $merchantPublicKey 商户公钥
     * - $merchantPrivateKey 商户私钥
     * - $yeepayPublicKey 大捷科技公钥
     *
     * @param string $account
     * @param string $merchantPublicKey
     * @param string $merchantPrivateKey
     * @param string $yeepayPublicKey
     * @param array $extraData
     */
    public function __construct($account,$merchantPublicKey,$merchantPrivateKey,$yeepayPublicKey,$extraData=array()){
        $this->account = $account;
        $this->merchantPublicKey = $merchantPublicKey;
        $this->merchantPrivateKey = $merchantPrivateKey;
        $this->yeepayPublicKey = $yeepayPublicKey;
        $this->RSA = new Crypt_RSA();
        $this->AES = new Crypt_AES();
        if ($extraData) {
            foreach ($extraData as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }


    /**
     * 支付
     * @param $order_id
     * @param $transtime
     * @param $amount
     * @param $product_catalog
     * @param $user_ip
     * @param $user_ua
     * @param string $callbackurl
     * @param string $fcallbackurl
     * @param int $currency
     * @param string $product_name
     * @param string $product_desc
     * @param $mer_tag
     * @param $bind_id
     * @return string
     */
    public function webPay($params){
        $this->checkParam($params);
        return $this->getUrl(YEEPAY_MOBILE_API, 'jlquick', $params);
    }


    /**
     * 微信支付
     * @param array $params
     * @return string
     */
    public function wxPay($params) {
        $this->checkParam($params);
        return $this->getUrl(YEEPAY_MOBILE_API, 'wxpay', $params);
    }

    /**
     * 支付宝支付
     * @param array $params
     * @return string
     */
    public function aliPay($params) {
        $this->checkParam($params);
        return $this->getUrl(YEEPAY_MOBILE_API, 'alipay', $params);
    }

    /**
     * 支付高级接口调用
     * @param array $params
     * @return array
     */
    public function wePay($params) {
        $method = $params['method'];
        unset($params['method']);
        $this->checkParam($params);
        return $this->post(YEEPAY_MOBILE_API, $method, $params);
    }

    /**
     * 必要参数检查
     * @param $params
     */
    public function checkParam($params)
    {
        /*交易时间(transtime)检查 1.时间戳整数 2.小于 当前系统时间+30分钟之内, */
        if(!is_int((int)$params['transtime']) || $params['transtime']>time()+30*60)
        {
            exit("参数错误");
        }

        /*url 合法性检查- 正常的url 字节不大于200*/
        $return_url = filter_var($params['fcallbackurl'], FILTER_VALIDATE_URL);
        if(!in_array(substr($return_url,0,5),array('http:','https')) || mb_strlen($params['fcallbackurl'])>200)
        {
            exit("参数错误");
        }
        $callback_url = filter_var($params['callbackurl'], FILTER_VALIDATE_URL);
        if(!in_array(substr($callback_url,0,5),array('http:','https')) || mb_strlen($params['callbackurl'])>200)
        {
            exit("参数错误");
        }
        /*金额 合法性 检查*/
        if(!(float)($params['amount']) == 1 || (float)($params['amount']) <1)
        {
            exit("参数错误");
        }
    }

    /*
     * 加密数据
     */
    public function makeBack($query)
    {
        $request = $this->buildRequest($query);
        return http_build_query($request);
    }

    public function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)self::objectToArray($v);
        }
        return $e;
    }

    /**
     * 查询绑卡信息列表，获取对应支付身份的绑卡id
     * @param $mer_tag
     * @return array
     * @throws yeepayMPayException
     */
    public function getBinds($mer_tag){
        $query = array(
            'mer_tag'	=>	(string)$mer_tag,
        );
        return $this->get(YEEPAY_MOBILE_API, 'getBinds', $query);
    }


    /**
     * 鉴权绑卡
     * @param $cardno
     * @param $name
     * @param $card_type
     * @param $certificate_type
     * @param $identity
     * @param $card_expire
     * @param $telno
     * @param $cvn2
     * @param $mer_tag
     * @return Ambigous|mixed
     * @throws yeepayMPayException
     */
    public function bind($cardno,$name,$card_type,$certificate_type,$identity,$card_expire,$telno,$cvn2,$mer_tag){
        $query = array (
            'cardno' => $cardno,
            'name' => $name,
            'card_type' => $card_type,
            'certificate_type' => $certificate_type,
            'identity' => $identity,
            'card_expire' => $card_expire,
            'telno' => $telno,
            'cvn2' => $cvn2,
            'mer_tag' => $mer_tag,
        );
        return $this->post(YEEPAY_MOBILE_API, 'bind', $query);
    }

    /**
     * 银行卡信息查询
     * @param string $cardno
     * @return array
     */
    public function getCard($cardno){
        $query = array(
            'cardno'=>$cardno
        );
        return $this->get(YEEPAY_MOBILE_API, 'getCard', $query);
    }

    /**
     * 解绑卡
     * @param $bindid
     * @param $cardno
     * @return Ambigous|mixed
     * @throws yeepayMPayException
     */
    public function unBind($bindid,$cardno)
    {
        $query = array(
            'bind_id'=>$bindid,
            'cardno'=>$cardno
        );
        return $this->post(YEEPAY_MOBILE_API, 'unBind', $query);
    }

    /**
     * 查询订单支付结果
     * @param $order_id
     * @return array
     * @throws yeepayMPayException
     */
    public function query($order_id){
        $query = array('order_id'=>(string)$order_id);
        return $this->get(YEEPAY_MOBILE_API, 'query', $query);
    }

    /**
     * 退货/退款
     * @param $amount
     * @param $order_id
     * @param $origorder_id
     * @param $tradetime
     * @param string $cause
     * @return Ambigous|mixed
     * @throws yeepayMPayException
     */
    public function refund($amount,$order_id,$origorder_id,$tradetime,$cause=''){
        $query = array(
            'amount'	=>	$amount,
            'cause'		=>	$cause,
            'trade_time'		=>	$tradetime,
            'order_id'	=>	(string)$order_id,
            'origorderid'	=>	(string)$origorder_id,
        );
        return $this->post(YEEPAY_MOBILE_API, 'refund', $query);
    }

    /**
     * 查询退款结果
     * @param $orderid
     * @return array
     * @throws yeepayMPayException
     */
    public function refundQuery($orderid)
    {
        $query = array('order_id'=>(string)$orderid);
        return $this->get(YEEPAY_MOBILE_API, 'refundQuery', $query);
    }


    /**
     * 获取对账单
     * @param $sta_date
     * @param $page
     * @return array
     * @throws yeepayMPayException
     */
    public function getBill($sta_date,$page){
        $query = array(
            'sta_date'	=>	(string)$sta_date,
            'page'	=>	(string)$page,
        );
        return $this->get(YEEPAY_MOBILE_API, 'getBill', $query);
    }


    /**
     * 回调返回数据解析函数
     * $data = $_POST['data']
     * $encryptkey = $_POST['encryptkey']
     *
     * @param string $data
     * @param string $encryptkey
     * @return array
     */
    public function callback($data,$encryptkey){
        return $this->parseReturn($data, $encryptkey);
    }
    protected function post($type,$method,$query){
        $request = $this->buildRequest($query);
        $url = $this->getAPIUrl($type,$method);
        $data = $this->http($url, 'POST',http_build_query($request));
        if($this->http_info['http_code'] == 405)
            throw new yeepayMPayException('此接口不支持使用POST方法请求',1004);
        return $this->parseReturnData($data);
    }
    /**
     * 使用GET的模式发出API请求
     *
     * @param string $type
     * @param string $method
     * @param array $query
     * @return array
     */
    protected function get($type,$method,$query){
        $request = $this->buildRequest($query);
        $url = $this->getAPIUrl($type,$method);
        $url .= '?'.http_build_query($request);
        $data = $this->http($url, 'GET');
        if($this->http_info['http_code'] == 405)
            throw new yeepayMPayException('此接口不支持使用GET方法请求',1003);
        return $this->parseReturnData($data);
    }

    /**
     * 返回请求URL地址
     * @param string $type
     * @param string $method
     * @param array $query
     * @return string
     */
    protected function getUrl($type,$method,$query){
        $request = $this->buildRequest($query);
        $url = $this->getAPIUrl($type,$method);
        $url .= '?'.http_build_query($request);
        return $url;
    }
    /**
     * 创建提交到大捷科技的最终请求
     *
     * @param array $query
     * @return array
     */
    protected function buildRequest(array $query){
        if(!array_key_exists('merchantaccount', $query))
            $query['merchantaccount'] = $this->account;
        $sign = $this->RSASign($query);
        $query['sign'] = $sign;
        $request = array();
        $request['merchantaccount'] = $this->account;
        $request['encryptkey'] = $this->getEncryptkey();
        $request['data'] = $this->AESEncryptRequest($query);
        return $request;
    }
    /**
     * 根据请求类型不同，返回完整API请求地址
     *
     * @param int $type
     * @param string $method
     * @return string
     */
    protected function getAPIUrl($type,$method){
        if ($type == YEEPAY_MOBILE_API)
            return $this->API_Mobile_Pay_Url.$method;
        else
            return $this->API_Mobile_Pay_Url.$method;
    }
    /**
     *
     * @param string $url
     * @param string $method
     * @param string $postfields
     * @return mixed
     */
    protected function http($url, $method, $postfields = NULL) {
        $this->http_info = array();
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        $method = strtoupper($method);
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields))
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($postfields))
                    $url = "{$url}?{$postfields}";
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        curl_close ($ci);
        return $response;
    }

    protected function parseReturnClearData($data){
        if(strpos($data, 'data')===true )
        {
            $return = json_decode($data,true);

            if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
                throw new yeepayMPayException($return['error_msg'],$return['error_code']);
            return $this->parseReturn($return['data'], $return['encryptkey']);
        }else{
            return $data;
        }
    }

    protected function parseReturnData($data){
        $return = json_decode($data,true);
        if (!is_array($return)) {
            return array();
        }
// 		if(array_key_exists('error_code', $return))
//		for api : query/order
        if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
            throw new yeepayMPayException($return['error_msg'],$return['error_code']);
        return $this->parseReturn($return['data'], $return['encryptkey']);
    }
    protected function parseReturn($data,$encryptkey){
        $AESKey = $this->getYeepayAESKey($encryptkey);
        $return = $this->AESDecryptData($data, $AESKey);
        $return = json_decode($return,true);
        if(!array_key_exists('sign', $return)){
            if(array_key_exists('error_code', $return))
                throw new yeepayMPayException($return['error_msg'],$return['error_code']);
            throw new yeepayMPayException('请求返回异常',1001);
        }else{
            if( !$this->RSAVerify($return, $return['sign']) )
                throw new yeepayMPayException('请求返回签名验证失败',1002);

//		if(array_key_exists('error_code', $return))
//		for api : query/order
            if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
                throw new yeepayMPayException($return['error_msg'],$return['error_code']);
            unset($return['sign']);
            return $return;
        }
    }

    /**
     * 生成一个随机的字符串作为AES密钥
     *
     * @param number $length
     * @return string
     */
    protected function generateAESKey($length=16){
        $baseString = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $AESKey = '';
        $_len = strlen($baseString);
        for($i=1;$i<=$length;$i++){
            $AESKey .= $baseString[rand(0, $_len-1)];
        }
        $this->AESKey = $AESKey;
        return $AESKey;
    }
    /**
     * 通过RSA，使用大捷科技公钥，加密本次请求的AESKey
     *
     * @return string
     */
    protected function getEncryptkey(){
        if(!$this->AESKey)
            $this->generateAESKey();
        $this->RSA->loadKey($this->makePem($this->yeepayPublicKey));
        $encryptKey = $this->RSA->encrypt($this->AESKey);
        return $encryptKey;
    }
    /**
     * 返回大捷科技返回数据的AESKey
     *
     * @param unknown $encryptkey
     * @return Ambigous <string, boolean, unknown>
     */
    protected function getYeepayAESKey($encryptkey){
        $this->RSA->loadKey($this->makePem($this->merchantPrivateKey,0,false));
        $yeepayAESKey = $this->RSA->decrypt($encryptkey);
        return $yeepayAESKey;
    }
    /**
     * 通过AES加密请求数据
     *
     * @param array $query
     * @return string
     */
    protected function AESEncryptRequest(array $query){
        if(!$this->AESKey)
            $this->generateAESKey();
        $this->AES->setKey($this->AESKey);

        return $this->AES->encrypt(json_encode($query));
    }
    /**
     * 通过AES解密大捷科技返回的数据
     *
     * @param string $data
     * @param string $AESKey
     * @return Ambigous <boolean, string, unknown>
     */
    protected function AESDecryptData($data,$AESKey){
        $this->AES->setKey($AESKey);
        return $this->AES->decrypt($data);
    }
    /**
     * 用RSA 签名请求
     *
     * @param array $query
     * @return string
     */
    protected function RSASign(array $query){
        if(array_key_exists('sign', $query))
            unset($query['sign']);
        ksort($query);
        //签名
        $sign ='';
        $privateKey = $this->makePem($this->merchantPrivateKey,0,false);
        $sign_flag = openssl_sign (join('',$query), $signature, openssl_pkey_get_private($privateKey), OPENSSL_ALGO_SHA1);
        if ($sign_flag) {
            $sign = base64_encode ( $signature );
        }
        return $sign;
    }
    /**
     * 使用大捷科技公钥检测大捷科技返回数据签名是否正确
     *
     * @param array $query
     * @param string $sign
     * @return boolean
     */
    protected function RSAVerify(array $return,$sign){
        if(array_key_exists('sign', $return))
            unset($return['sign']);
        ksort($return);
        foreach ($return as $k=>$val){
            if( is_array($val) )
                $return[$k] = self::cn_json_encode($val);
        }
        return openssl_verify(join('',$return),base64_decode($sign),openssl_pkey_get_public($this->makePem($this->yeepayPublicKey,0)),OPENSSL_ALGO_SHA1);
    }
    /*
     * 转换字符串为公私钥
     */
    protected function makePem($str,$page=0,$isPubKey=true)
    {
        $length = strlen($str);
        $perLength = 65;
        if($page == 0) {
            if ($isPubKey) {
                $pem = '-----BEGIN PUBLIC KEY-----' . "\r\n";
            } else {
                $pem = '-----BEGIN RSA PRIVATE KEY-----' . "\r\n";
            }
        }else{
            $pem='';
        }
        $start = $page*$perLength;
        $pem .= substr($str,$start,$perLength)."\r\n";
        if($perLength*($page+1)<$length)
        {
            $pem .= $this->makePem($str,$page+1,$isPubKey);
        }
        else
        {
            $str = $pem;
            if($isPubKey)
            {
                $str .= '-----END PUBLIC KEY-----';
            }
            else{
                $str .= '-----END RSA PRIVATE KEY-----';
            }

            $pem = $str;
        }
        return $pem;
    }
    public static function cn_json_encode($value){
        if (defined('JSON_UNESCAPED_UNICODE'))
            return json_encode($value,JSON_UNESCAPED_UNICODE);
        else{
            $encoded = urldecode(json_encode(self::array_urlencode($value)));
            return preg_replace(array('/\r/','/\n/'), array('\\r','\\n'), $encoded);
        }
    }
    public static function array_urlencode($value){
        if (is_array($value)) {
            return array_map(array('yeepayMPay','array_urlencode'),$value);
        }elseif (is_bool($value) || is_numeric($value)){
            return $value;
        }else{
            return urlencode(addslashes($value));
        }
    }
    /**
     * Get the header info to store.
     */
    public function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * 校验输入的有效期，并将常见的几种错误输入方式进行纠正
     * 	- 01/14 模式，去掉 / 线
     * 	- 1401、14/01 模式，判断是年月先后顺序输入错误，并去掉 / 线
     *
     * @param string $validthru
     * @return boolean
     */
    public static function checkValidthru(&$validthru){
        if( !preg_match('/^(\d{2})(\d{2})$/',$validthru,$matches)){
            if(!preg_match('/^(\d{2})\/(\d{2})$/', $validthru,$matches))
                return false;
            $validthru = $matches[1].$matches[2];
        }
        if($matches[1]<=12 && $matches[2]>=13)
            return true;
        if($matches[1] > 12 && $matches[2] < 13){
            $validthru = $matches[2].$matches[1];
            return true;
        }
        return false;
    }
    /**
     * 校验CVV2有效性
     * 	- 3位数字
     *
     * @param string $cvv2
     * @return boolean
     */
    public static function checkCvv2($cvv2){
        if(preg_match('/^\d{3}$/', $cvv2))
            return true;
        return false;
    }
}