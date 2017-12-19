<?php
/**
 * 公共函数类
 */

/**
 * 生成订单唯一编号
 * @return  string
 */
function get_sn($prefix='')
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    return $prefix.date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}
/**
 * 生成订单商品唯一编码
 * @return string
 */
function get_goods_key() {
    $yCode   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $result = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $result;
}
/**
 * 获取jwt信息
 * $params array|string 参数名
 * @return  string
 */
function get_jwt($params=null)
{
    $payload = \JWTAuth::parseToken()->getPayload();
    return  $payload->get($params);
}
/**
 * 获取月初，下月初
 * @return mixed
 */
function get_month($time=null)
{
    if (is_null($time)) {
        $time=time();
    }
    $year=date('Y',$time);
    $month=date('m',$time);
    $return['this_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month-1,1,$year));
    $return['next_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month,1,$year));
    return $return;
}

/**
 * 获取传过来的时间的月初，下月初
 * @return mixed
 */
function get_month_time()
{
    $time=time();
    $year=date('Y',$time);
    $month=date('m',$time);
    $return['this_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month,1,$year));
    $return['next_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month+1,1,$year));
    return $return;
}
/**
 * 获取jwt信息中的user_id
 * @return  string
 */
function get_user_id()
{
    $payload = \JWTAuth::parseToken()->getPayload();
    return  $payload->get('user_id');
}

/**
 * 获取jwt信息中的admin_id
 * @return  string
 */
function get_admin_id()
{
    $payload = \JWTAuth::parseToken()->getPayload();
    return  $payload->get('admin_id');
}
/**
 * 批量修改
 * @param string $tableName
 * @param array $multipleData
 * @return bool|int
 */
function update_batch($tableName = "", $multipleData = array()){

    if( $tableName && !empty($multipleData) ) {
        $updateColumn = array_keys($multipleData[0]);
        $referenceColumn = $updateColumn[0]; //e.g id
        unset($updateColumn[0]);
        $whereIn = "";
        $q = "UPDATE ".$tableName." SET ";
        foreach ( $updateColumn as $uColumn ) {
            $q .=  $uColumn." = CASE ";
            foreach( $multipleData as $data ) {
                $q .= "WHEN ".$referenceColumn." = '".$data[$referenceColumn]."' THEN '".$data[$uColumn]."' ";
            }
            $q .= "ELSE ".$uColumn." END, ";
        }
        foreach( $multipleData as $data ) {
            $whereIn .= "'".$data[$referenceColumn]."', ";
        }
        $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
        // Update
        return \DB::update(\DB::raw($q));
    } else {
        return false;
    }
}
/**
 * 获取jwt信息中的supplier_id
 * @return  string
 */
function get_supplier_id()
{
    $payload = \JWTAuth::parseToken()->getPayload();
    return  $payload->get('supplier_id');
}
/**
 * get请求
 * @return  array
 */
function vget($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	$response = curl_exec($ch);
	curl_close($ch);
	//-------请求为空
	if(empty($response)){
		return null;
	}
	return $response;
}

/**
 * post请求
 * @return  array
 */
function vpost($url,$data){
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/**
 * 随机生成一个字符串
 * @param $length 长度
 * @return string
 */
function getRandomkeys($length = 8)
{
    $key = "";
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXY';    //字符池
    for($i=0; $i<$length; $i++)
    {
        $key .= $pattern{mt_rand(0,60)};    //生成php随机数
    }
    return $key;
}
/**
 * 获取常量的缓存
 * @param $key
 * @param $type
 * $key包含['statement_date','repayment_date']
 * $type包含['credit']
 * @return mixed
 */
function get_constant_cache($key,$type)
{
    return BackendConstantService::getConstantCache($key,$type);
}

/**
 * 批量插入SQL拼接
 * @param $field
 * @param $params
 * @return array
 */
function sql_batch_str($field, $params) {
    $data = [];
    foreach ($params as $key => $value) {
        $data_info = [];
        foreach ($value as $k1 => $v1) {
            foreach ($field as $k2 => $v2) {
                if ($k1 == $k2) {
                    $data_info[$v2] = $v1;
                    continue;
                }
            }
        }
        $data[] = $data_info;
    }
    return $data;
}

/**
 * 打印sql到日志
 * 直接把print_sql放到要打印的sql语句前面就可以了
 */
function print_sql()
{
    \DB::listen(function ($sql) {
        foreach ($sql->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $sql->bindings[$i] = "'$binding'";
                }
            }
        }        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
        $query = vsprintf($query, $sql->bindings);        // Save the query to file
        $logFile = fopen(
            storage_path('logs' . DIRECTORY_SEPARATOR . 'query.log'), 'a+'
        );
        fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
        fclose($logFile);
    }
    );
}

/**
 * 自定义缓存配置
 * @return mixed
 */
function rewrite_cache()
{
    return \Cache::store(\Config::get('cache.cache_type'));
}
/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function get_ip()
{
    static $realip = NULL;
    if($realip !== NULL){
        return $realip;
    }
    if(isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);
                if($ip != 'unknown')
                {
                    $realip = $ip;
                    break;
                }
            }
        }
        elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if(isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if(getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}