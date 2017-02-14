<?php
/**
 * 获取指定位置的导航菜单
 * @param int $type 导航位置
 */
function WSTNavigation($type=0){
	$URL_HTML_SUFFIX = C('URL_HTML_SUFFIX');
	$cururl =  U(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME);
	$cururl = str_ireplace(".".$URL_HTML_SUFFIX,'',$cururl);
	$areaId2 = (int)session('areaId2');
	//$rs = F('navigation/'.$areaId2);
	if(!$rs){
		$m = M();
		//获取所在省份
	    $sql = "select parentId from __PREFIX__areas where areaId=".$areaId2;
		$areaId1Rs = $m->query($sql);
		$areaId1 = (int)$areaId1Rs[0]['parentId'];
		$sql = "select navType,navTitle,navUrl,isShow,isOpen 
		  from __PREFIX__navs where isShow=1 and (areaId1=0 or areaId1=".$areaId1.") and (areaId2=0 or areaId2=".$areaId2.") 
		  order by navType asc,navSort asc";
	    $rs = $m->query($sql);
	    F('navigation/'.$areaId2,$rs);
	}
	foreach ($rs as $key =>$v){
		$rs[$key]['url'] = $cururl;
		if(stripos($v['navUrl'],'https://')===false &&  stripos($v['navUrl'],'http://')===false){
			$rs[$key]['navUrl'] = WSTDomain()."/".$rs[$key]['navUrl'];
		}
		$rs[$key]['active'] = (stripos($rs[$key]['navUrl'],$cururl)!==false)?1:0;
		$rs[$key]['end'] = ($key==count($rs)-1)?1:0;
	}
	//分组
	$data = array();
	foreach ($rs as $key =>$v){
		$data[$v['navType']][] = $v;
	}
	return $data[$type];
}

/**
 * 货币枨式化
 * @param unknown $number
 */
function WSTMoney($number,$lc="en_US"){
	setlocale(LC_MONETARY, $lc);
	return money_format("%=*(#10.2n", $number);
}

/**
 * 获取首页商品分类列表
 */
function WSTGoodsCats(){
	//取消缓存？
    //$cats = S("WST_CACHE_GOODS_CAT_WEB");
	if(!$cats){
		$m = M();
		$sql = "select catId,catName from __PREFIX__goods_cats WHERE parentId = 0 AND isShow =1 AND catFlag = 1 order by catSort asc";
		$rs1 = $m->query($sql);
		$cats = array();
		for ($i = 0; $i < count($rs1); $i++) {
			$cat1Id = $rs1[$i]["catId"];
			$sql = "select catId,catName from __PREFIX__goods_cats WHERE parentId = $cat1Id AND isShow =1 AND catFlag = 1 order by catSort asc";
			$rs2 = $m->query($sql);
			$cats2 = array();
			for ($j = 0; $j < count($rs2); $j++) {
				$cat2Id = $rs2[$j]["catId"];
				$sql = "select catId,catName from __PREFIX__goods_cats WHERE parentId = $cat2Id AND isShow =1 AND catFlag = 1 order by catSort asc";
				$rs3 = $m->query($sql);
				$cats3 = array();
				for ($k = 0; $k < count($rs3); $k++) {
					$cats3[] = $rs3[$k];
				}
				$rs2[$j]["catChildren"] = $cats3;
				$cats2[] = $rs2[$j];
			}
			$rs1[$i]["catChildren"] = $cats2;
			$cats[] = $rs1[$i];
		}
		//S("WST_CACHE_GOODS_CAT_WEB".$areaId2,$cats,31536000);
	}
	return $cats;
}

/**
 * 获取购物车数量
 */
function WSTCartNum(){
	$shopcart = session("WST_CART")?session("WST_CART"):array();
	return count($shopcart);
}

//精确到毫秒的时间戳转换
function microt($time,$x=null){
	$len=strlen($time);
	if($len<13){
		$time=$time."0";
	}
	$list=explode(".",$time);
	if($x=="L"){
		return date("His",$list[0]).substr($list[1],0,3);
	}else if($x=="Y"){
		return date("Y-m-d",$list[0]);
	}else if($x=="H"){
		return date("H:i:s",$list[0]).".".substr($list[1],0,3);
	}else if($x=="r"){
		return date("Y年m月d日 H:i",$list[0]);
	}else{
		return date("Y-m-d H:i:s",$list[0]).".".substr($list[1],0,3);
	}
}

/**
 * 获取用户昵称
* uid 用户id，或者 用户数组
* type 获取的类型, userName,userEmail,userPhone
* key  获取完整用户名, sub 截取,all 完整
*/
function get_user_name($uid='',$type='userName',$key='sub'){
	if(is_array($uid)){
		if(isset($uid['userName']) && !empty($uid['userName'])){
			return $uid['userName'];
		}
		if(isset($uid['userEmail']) && !empty($uid['userEmail'])){
			if($key=='sub'){
				$email = explode('@',$uid['userEmail']);
				return $uid['userEmail'] = substr($uid['userEmail'],0,2).'*'.$email[1];
			}else{
				return $uid['userEmail'];
			}
		}
		if(isset($uid['userPhone']) && !empty($uid['userPhone'])){
			if($key=='sub'){
				return $uid['userPhone'] = substr($uid['userPhone'],0,3).'****'.substr($uid['userPhone'],7,4);
			}else{
				return $uid['userPhone'];
			}
		}
		return '';
	}else{
		$uid = intval($uid);
		$info = M('Users')->where(array('userId' => $uid))->field(array('userName','userEmail','userPhone'))->find();
		if(isset($info['userName']) && !empty($info['userName'])){
			return $info['userName'];
		}

		if(isset($info['userEmail']) && !empty($info['userEmail'])){
			if($key=='sub'){
				$email = explode('@',$info['userEmail']);
				return $info['userEmail'] = substr($info['userEmail'],0,2).'*'.$email[1];
			}else{
				return $info['userEmail'];
			}
		}
		if(isset($info['userPhone']) && !empty($info['userPhone'])){
			if($key=='sub'){
				return $info['userPhone'] = substr($info['userPhone'],0,3).'****'.substr($info['userPhone'],7,4);
			}else{
				return $info['userPhone'];
			}
		}
		if(isset($info[$type]) && !empty($info[$type])){
			return $info[$type];
		}
		return null;
	}
}

/*
 * 获取用户信息
*/
function get_user_key($uid='',$type='img',$size=''){

	if(empty($uid) && $type == 'img'){
		return 'Upload/member.jpg';
	}

	if(is_array($uid)){
		if(isset($uid[$type])){
			if($type=='img'){
				$fk = explode('.',$uid[$type]);
				$h = array_pop($fk);
				if($size){
					return $uid['userPhoto'].'_'.$size.'.'.$h;
				}else{
					return $uid['userPhoto']?$uid['userPhoto']:'Upload/member.jpg';
				}
			}
			return $uid['userPhoto']?$uid['userPhoto']:'Upload/member.jpg';
		}
		return 'null';
	}else{
		$uid = intval($uid);
		$info = M('users')->where(array('userId' => $uid))->find();
		if($type=='img'){
			$fk = explode('.',$info['userPhoto']);
			$h = array_pop($fk);
			if($size){
				return $info['userPhoto'].'_'.$size.'.'.$h;
			}else{
				return $info['userPhoto']?$info['userPhoto']:'Upload/member.jpg';
			}
		}
		if(isset($info[$type])){
			return $info[$type];
		}
		return null;
	}
}

//生成订单号  100000000
function pay_get_dingdan_code($dingdanzhui=''){
	return $dingdanzhui.time().substr(microtime(),2,6).rand(0,9);
}
/* 获取ip + 地址*/
function _get_ip_dizhi($ip=null){
	$opts = array(
			'http'=>array(
					'method'=>"GET",
					'timeout'=>5,)
	);
	$context = stream_context_create($opts);
	if($ip){
		$ipmac = $ip;
	}else{
		//$ipmac=_get_ip();
		$ipmac=get_client_ip();
		if(strpos($ipmac,"127.0.0.") === true)return '';
	}

	$url_ip='http://ip.taobao.com/service/getIpInfo.php?ip='.$ipmac;
	$str = @file_get_contents($url_ip, false, $context);
	if(!$str) return "";
	$json=json_decode($str,true);
	if($json['code']==0){

		$json['data']['region'] = addslashes(_htmtocode($json['data']['region']));
		$json['data']['city'] = addslashes(_htmtocode($json['data']['city']));

		$ipcity= $json['data']['region'].$json['data']['city'];
		$ip= $ipcity.','.$ipmac;
	}else{
		$ip="";
	}
	return $ip;
}
/**
 *   生成购买的抢购码
*	user_num 		@生成个数	如果购买1个，生成一个抢购码
*	shopinfo		@商品信息	商品订单信息
*	ret_data		@返回信息
*/
function pay_get_shop_codes($user_num=1,$shopinfo=null,&$ret_data=null){
	$db = new \Think\Model();
	$ret_data['query'] = true;
	$table = $shopinfo['codes_table'];
	$codes_arr = array();
	//取出最后一条
	$codes_one = $db->query("select id,s_id,s_cid,s_len,s_codes from `__PREFIX__$table` where `s_id` = '$shopinfo[id]' order by `s_cid` DESC  LIMIT 1 for update");
	$codes_one = current($codes_one);
	$codes_arr[$codes_one['s_cid']] = $codes_one;
	$codes_count_len = $codes_arr[$codes_one['s_cid']]['s_len'];
	//如果剩余抢购码长 小于 购物长度
	if($codes_count_len < $user_num && $codes_one['s_cid'] > 1){
		for($i=$codes_one['s_cid']-1;$i>=1;$i--){
			$ab = $db->query("select id,s_id,s_cid,s_len,s_codes from `__PREFIX__$table` where `s_id` = '$shopinfo[id]' and `s_cid` = '$i'  LIMIT 1 for update");
			$codes_arr[$i] = current($ab);
			$codes_count_len += $codes_arr[$i]['s_len'];
			if($codes_count_len > $user_num)  break;
		}
	}

	if($codes_count_len < $user_num) $user_num = $codes_count_len;

	$ret_data['user_code'] = '';
	$ret_data['user_code_len'] = 0;

	foreach($codes_arr as $icodes){
		$u_num = $user_num;
		$icodes['s_codes'] = unserialize($icodes['s_codes']);
		$code_tmp_arr = array_slice($icodes['s_codes'],0,$u_num);
		$ret_data['user_code'] .= implode(',',$code_tmp_arr);
		$code_tmp_arr_len = count($code_tmp_arr);

		if($code_tmp_arr_len < $u_num){
			$ret_data['user_code'] .= ',';
		}

		$icodes['s_codes'] = array_slice($icodes['s_codes'],$u_num,count($icodes['s_codes']));
		$icode_sub = count($icodes['s_codes']);
		$icodes['s_codes'] = serialize($icodes['s_codes']);

		if(!$icode_sub){
			$query = $db->execute("UPDATE `__PREFIX__$table` SET `s_cid` = '0',`s_codes` = '$icodes[s_codes]',`s_len` = '$icode_sub' where `id` = '$icodes[id]'");
			if(!$query)$ret_data['query'] = false;
		}else{
			$query = $db->execute("UPDATE `__PREFIX__$table` SET `s_codes` = '$icodes[s_codes]',`s_len` = '$icode_sub' where `id` = '$icodes[id]'");
			if(!$query)$ret_data['query'] = false;
		}
		$ret_data['user_code_len'] += $code_tmp_arr_len;
		$user_num  = $user_num - $code_tmp_arr_len;
	}

}