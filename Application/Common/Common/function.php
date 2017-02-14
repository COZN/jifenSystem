<?php

/**
 * 判断是否手机访问
 */
function WSTIsMobile() {
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
    $mobile_browser = '0';  
    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
       $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
       $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
       $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
       $mobile_browser++;  
       $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
       $mobile_agents = array(  
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
		    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
		    'wapr','webc','winw','winw','xda','xda-'
	   );  
    if(in_array($mobile_ua, $mobile_agents))$mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)$mobile_browser++;  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)$mobile_browser=0;  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)$mobile_browser++;  
    if($mobile_browser>0){  
       return true;  
    }else{
       return false;
    }
}

/**
 * 邮件发送函数
 * @param string to      收件人
 * @param string subject 邮件标题
 * @param string content 邮件内容
 * @return array
 */
function WSTSendMail($to, $subject, $content) {
	require_cache(VENDOR_PATH."PHPMailer/smtp.class.php");
    require_cache(VENDOR_PATH."PHPMailer/phpmailer.class.php");
    $mail = new phpmailer();
	$config = system_config();
    // 装配邮件服务器
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = $config['mailSmtp'];
    $mail->SMTPAuth = $config['mailAuth'];
    $mail->Username = $config['mailUserName'];
    $mail->Password = $config['mailPassword'];
    $mail->CharSet = 'utf-8';
    // 装配邮件头信息
    $mail->From = $config['mailAddress'];
    $mail->AddAddress($to);
    $mail->FromName = $config['mailSendTitle'];
    $mail->IsHTML(true);
    // 装配邮件正文信息
    $mail->Subject = $subject;
    $mail->Body = $content;
    // 发送邮件
    $rs =array();
    if (!$mail->Send()) {
    	$rs['status'] = 0;
    	$rs['msg'] = $mail->ErrorInfo;
        return $rs;
    } else {
    	$rs['status'] = 1;
        return $rs;
    }
}

function system_config(){
	$temp = M('sys_configs')->select();
	$config = array();
	foreach ($temp as $key => $value){
		$config[$value['fieldCode']] = $value['fieldValue'];
	}
	return $config;
}

/**
 * 发送短信
 * @param string $mobile  手机号码
 * @param string $content     短信内容
 */
function WSTSendSMS($mobile,$content){
	require_cache(VENDOR_PATH."Sms/sms.class.php");
	if(!$mobile){
		return array('status' => 0,'msg' => "手机号码不能为空！");
	}
	if(!$content){
		return array('status' => 0,'msg' => "短信内容不能为空！");
	}
	$config = system_config();
	if (empty($config) || empty($config['smsKey']) || empty($config['smsPass'])){
		return array('status' => 0,'msg' => "短信功能未开启");
	}
	if (empty($config['smsSign'])){
		return array('status' => 0,'msg' => "短信签名不能为空");
	}
	$sends = new sms($config['smsKey'],$config['smsPass']);
	//返回0发送成功
	$status = $sends->sendSms($content,$mobile);
	if ($status == 0){
		return array('status' => 1,'msg' => '验证码发送成功');
	}else{
		return array('status' => 0,'msg' => '验证码发送失败');
	}
}

/*HTML安全过滤*/
function _htmtocode($content) {
	$content = str_replace('%','%&lrm;',$content);
	$content = str_replace("<", "&lt;", $content);
	$content = str_replace(">", "&gt;", $content);
	$content = str_replace("\n", "<br/>", $content);
	$content = str_replace(" ", "&nbsp;", $content);
	$content = str_replace('"', "&quot;", $content);
	$content = str_replace("'", "&#039;", $content);
	$content = str_replace("$", "&#36;", $content);
	$content = str_replace('}','&rlm;}',$content);
	return $content;
}

/**
 * 字符串替换
 * @param string $str     要替换的字符串
 * @param string $repStr  即将被替换的字符串
 * @param int $start      要替换的起始位置,从0开始
 * @param string $splilt  遇到这个指定的字符串就停止替换
 */
function WSTStrReplace($str,$repStr,$start,$splilt = ''){
	$newStr = substr($str,0,$start);
	$breakNum = -1;
	for ($i=$start;$i<strlen($str);$i++){
		$char = substr($str,$i,1);
		if($char==$splilt){
			$breakNum = $i;
			break;
		}
		$newStr.=$repStr;
	}
	if($splilt!='' && $breakNum>-1){
		for ($i=$breakNum;$i<strlen($str);$i++){
			$char = substr($str,$i,1);
			$newStr.=$char;
		}
	}
	return $newStr;
}
/**
 * 循环删除指定目录下的文件及文件夹
 * @param string $dirpath 文件夹路径
 */
function WSTDelDir($dirpath){
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!==false) {
		if($file!="." && $file!="..") {
		    $fullpath=$dirpath."/".$file;
		    if(!is_dir($fullpath)) {
		        unlink($fullpath);
		    } else {
		        WSTDelDir($fullpath);
		        rmdir($fullpath);
		    }
	    }
	}	 
	closedir($dh);
    $isEmpty = 1;
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!== false) {
		if($file!="." && $file!="..") {
			$isEmpty = 0;
			break;
		}
	}
	return $isEmpty;
}
/**
 * 获取网站域名
 */
function WSTDomain(){
	$server = $_SERVER['HTTP_HOST'];
	$http = is_ssl()?'https://':'http://';
	return $http.$server.__ROOT__;
}
/**
 * 获取系统根目录
 */
function WSTRootPath(){
	return dirname(dirname(dirname(dirname(__File__))));
}
/**
 * 获取网站根域名
 */
function WSTRootDomain(){
	$server = $_SERVER['HTTP_HOST'];
	$http = is_ssl()?'https://':'http://';
	return $http.$server;
}
/**
 * 设置当前页面对象
 * @param int 0-用户  1-商家
 */
function WSTLoginTarget($target = 0){
	$WST_USER = session('WST_USER');
	$WST_USER['loginTarget'] = $target;
	session('WST_USER',$WST_USER);
}

/**
 * 生成缓存文件
 */
function WSTDataFile($name, $path = '',$data=array()){
	$key = C('DATA_CACHE_KEY');
	$name = md5($key.$name);
	if(is_array($data) && !empty($data)){
		if($data['mallLicense']==''){
			if(stripos($data['mallTitle'],'Powered By o2omall')===false)$data['mallTitle'] = $data['mallTitle']." - Powered By o2omall";
		}
	    $data   =   serialize($data);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(C('DATA_CACHE_CHECK')) {//开启数据校验
            $check  =  md5($data);
        }else {
            $check  =  '';
        }
        $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
        $result  =   file_put_contents(DATA_PATH.$path.$name.".php",$data);
	    clearstatcache();
	}else if(is_null($data)){
	    unlink(DATA_PATH.$path.$name.".php");
	}else{
		if(file_exists(DATA_PATH.$path.$name.'.php')){
		    $content    =   file_get_contents(DATA_PATH.$path.$name.'.php');
            if( false !== $content) {
	            $expire  =  (int)substr($content,8, 12);
	            if(C('DATA_CACHE_CHECK')) {//开启数据校验
	                $check  =  substr($content,20, 32);
	                $content   =  substr($content,52, -3);
	                if($check != md5($content)) {//校验错误
	                    return null;
	                }
	            }else {
	            	$content   =  substr($content,20, -3);
	            }
	            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
	                //启用数据压缩
	                $content   =   gzuncompress($content);
	            }
	            $content    =   unserialize($content);
	            return $content;
	        }
		}
		return null;
	}
}



/**
 * 建立文件夹
 * @param string $aimUrl
 * @return viod
 */
function WSTCreateDir($aimUrl) {
	$aimUrl = str_replace('', '/', $aimUrl);
	$aimDir = '';
	$arr = explode('/', $aimUrl);
	$result = true;
	foreach ($arr as $str) {
		$aimDir .= $str . '/';
		if (!file_exists_case($aimDir)) {
			$result = mkdir($aimDir,0777);
		}
	}
	return $result;
}

/**
 * 建立文件
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function WSTCreateFile($aimUrl, $overWrite = false) {
	if (file_exists_case($aimUrl) && $overWrite == false) {
		return false;
	} elseif (file_exists_case($aimUrl) && $overWrite == true) {
		WSTUnlinkFile($aimUrl);
	}
	$aimDir = dirname($aimUrl);
	WSTCreateDir($aimDir);
	touch($aimUrl);
	return true;
}

/**
 * 删除文件
 * @param string $aimUrl
 * @return boolean
 */
function WSTUnlinkFile($aimUrl) {
	if (file_exists_case($aimUrl)) {
		unlink($aimUrl);
		return true;
	} else {
		return false;
	}
}

function  WSTLogResult($filepath,$word){
	if(!file_exists_case($filepath)){
		WSTCreateFile($filepath);
	}
	$fp = fopen($filepath,"a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y-%m-%d %H:%M:%S",time())."\n".$word."\n\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}

function WSTReadExcel($file){
	Vendor("PHPExcel.PHPExcel");
	Vendor("PHPExcel.PHPExcel.IOFactory");
	return PHPExcel_IOFactory::load(WSTRootPath()."/Upload/".$file);
}

function GetIpLookup($ip = ''){
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    return $json;
}

function p($params){
	echo '<pre>';
	print_r($params);
}


/**
 * 动态生成表
 * @return boolean|string
 */
function content_get_codes_table(){
	$conftable = include APP_PATH.'/Common/Conf/table.php';
	$table = C('DB_PREFIX').'shopcodes_'.$conftable['num'];
	$model = new \Think\Model();
	$shopcodes_table_status = $model->query("SHOW TABLE STATUS LIKE '".$table."'");
	if(!$shopcodes_table_status) return false;
	if($shopcodes_table_status[0]['Auto_increment'] >= 99999){
		$num = intval($conftable['num'])+1;
		$shopcodes_table = 'shopcodes_'.$num;
		$create_table = C('DB_PREFIX').$shopcodes_table;
		$q1 = $model->execute("
				CREATE TABLE `$create_table` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`s_id` int(10) unsigned NOT NULL,
				`s_cid` smallint(5) unsigned NOT NULL,
				`s_len` smallint(5) DEFAULT NULL,
				`s_codes` text,
				`s_codes_tmp` text,
				PRIMARY KEY (`id`),
				KEY `s_id` (`s_id`),
				KEY `s_cid` (`s_cid`),
				KEY `s_len` (`s_len`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		$q2 = file_put_contents(APP_PATH.'/Common/Conf/table.php', "<?php\r\n return array('num'=>".$num.");\r\n?>");
		if(!$q1 || !$q2)return false;
	}else{
		$num = intval($conftable['num']);
		$shopcodes_table = 'shopcodes_'.$num;
	}
	return $shopcodes_table;
}


/**
 生成夺宝码
CountNum @ 生成个数
len @生成长度
sid	@商品ID
*/
function content_get_go_codes($CountNum=null,$len=null,$sid=null){
	$conftable = include APP_PATH.'/Common/Conf/table.php';
	if (empty($conftable)) return false;
	$table = C('DB_PREFIX').'shopcodes_'.$conftable['num'];
	$num = ceil($CountNum/$len);
	$code_i = $CountNum;
	$model = new \Think\Model();
	//$CountNum 小于3000时执行
	if($num == 1){
		$codes=array();
		for($i=1;$i<=$CountNum;$i++){
			$codes[$i]=10000000+$i;
		}
		shuffle($codes);
		$codes = serialize($codes);
		$query = $model->execute("INSERT INTO `$table` (`s_id`, `s_cid`, `s_len`, `s_codes`,`s_codes_tmp`) VALUES ('$sid', '1','$CountNum','$codes','$codes')");
		unset($codes);
		return $query;
	}

	$query_1 = true;
	// $num = 2; 100
	for($k=1;$k<$num;$k++){
		$codes=array();
		for($i=1;$i<=$len;$i++){
			$codes[$i] = 10000000+$code_i;
			$code_i--;
		}
		shuffle($codes);
		$codes=serialize($codes);
		$query_1 = $model->execute("INSERT INTO `$table` (`s_id`, `s_cid`, `s_len`, `s_codes`,`s_codes_tmp`) VALUES ('$sid', '$k','$len','$codes','$codes')");
		unset($codes);
	}

	$CountNum = $CountNum -(($num-1)*$len);
	$codes=array();

	for($i=1;$i<=$CountNum;$i++){
		$codes[$i]=10000000+$code_i;
		$code_i--;
	}
	shuffle($codes);
	$codes=serialize($codes);
	$query_2 = $model->execute("INSERT INTO `$table` (`s_id`, `s_cid`,`s_len`, `s_codes`,`s_codes_tmp`) VALUES ('$sid', '$num','$CountNum','$codes','$codes')");
	unset($codes);
	return $query_1 && $query_2;
}

/**
 * 根据登录用户的IP返回是否允许登录
 * @return bool
 */
function checkIp(){
	if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else
		$ip = "Unknow";

	$ips = new Admin\Model\LogIpModel();
	$data = $ips->getCatAndChild();
	$ipArray = array();
	foreach ($data as $v){
		$ipArray[] = $v['ip'];
	}

	if(in_array($ip,$ipArray)){
		return true;
	}else{
		return false;
	}
}

