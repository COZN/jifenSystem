<?php
namespace Admin\Controller;
/**
 * 首页（默认）控制器
 */
class IndexController extends BaseController {
	/**
	 * 跳到商城首页
	 */
    public function index(){
    	$this->isLogin();
        $this->display("/index");
    }
    /**
     * 跳去后台主页面
     */
    public function toMain(){
    	$this->isLogin();
        $m = D('Index');
        $weekInfo = $m->getWeekInfo();//一周动态
        $this->assign('weekInfo',$weekInfo);
        $sumInfo = $m->getSumInfo();//一周动态
        $this->assign('sumInfo',$sumInfo);
    	$this->display("/main");
    }
    /**
     * 跳去商城配置界面
     */
    public function toMallConfig(){
    	$this->isLogin();
    	$this->checkPrivelege('scxx_00');
    	$m = D('Admin/Index');
    	$this->assign('configs',$m->loadConfigsForParent());
    	//获取地区信息
		$m = D('Admin/Areas');
		$this->assign('areaList',$m->queryShowByList(0));
		$areaId2 = intval($GLOBALS['CONFIG']['defaultCity'])>0?$GLOBALS['CONFIG']['defaultCity']:(int)C('DEFAULT_CITY');
		if($areaId2>0){
			$area = $m->get($areaId2);
			$this->assign('areaId1',$area['parentId']);
		}
    	$this->display("/mall_config");
    }
    /**
     * 保存商城配置信息
     */
    public function saveMallConfig(){
    	$this->isAjaxLogin();
    	$this->checkAjaxPrivelege('scxx_02');
    	$m = D('Admin/Index');
    	$rs = $m->saveConfigsForCode();
    	$this->ajaxReturn($rs);
    }
    /**
     * 跳去登录页面
     */
    public function toLogin(){
    	$this->display("/login");
    }
    /**
     * 职员登录
     */
    public function login(){
    	$m = D('Admin/Staffs');
        if(I('loginName') != 'admin'){
            if(checkIp() == true) {
                $rs = $m->login();
                if ($rs['status'] == 1) {
                    session('WST_STAFF', $rs['staff']);
                    unset($rs['staff']);
                }
            }else{
                $rs['status'] = -1;
            }
        }else {
            $rs = $m->login();
            if ($rs['status'] == 1) {
                session('WST_STAFF', $rs['staff']);
                unset($rs['staff']);
            }
        }
        $this->ajaxReturn($rs);
    }
    /**
     * 离开系统
     */
    public function logout(){
    	session('WST_STAFF',null);
    	$this->redirect("Index/toLogin");
    }
    /**
     * 获取定时任务
     */
    public function getTask(){
        $this->isAjaxLogin();
        $rd = array('status'=>1);
        //获取待审核商品
        $m = D('Admin/Goods');
        $grs = $m->queryPenddingGoodsNum();
        $rd['goodsNum'] = $grs['num'];
        $rd['goodsSum'] = $rd['goodsNum'];
        //获取待审核店铺
        $m = D('Admin/Shops');
        $srs = $m->queryPenddingShopsNum();
        $rd['sshopsNum'] = $srs['num'];
        $rd['shopsNum'] = $rd['sshopsNum'];
        //获取退款列表
        $m = D('Admin/Orders');
        $refundRs = $m->queryIsRefundNum();
        $rd['refundNum'] = $refundRs['num'];
        $rd['ordersNum'] = $rd['refundNum'];
        //获取待受理和已受理的积分订单
        $m = D('Admin/IntegralOrders');
        $integralRs = $m->queryIntegralOrders();
        $rd['integralOrdersNum'] = $integralRs['num'];
        $rd['integralNum'] = $rd['integralOrdersNum'];
        $rd['isHandleNum'] = $integralRs['isHandle'];
        $rd['notHandleNum'] = $integralRs['notHandle'];
        //获取促销管理
        $m = D('Admin/Group');
        $groupRs = $m->queryPenddingGroupNum();
        $m = D('Admin/Seckill');
        $seckillRs = $m->queryPenddingSeckillNum();
        $m = D('Admin/Auction');
        $auctionRs = $m->queryPenddingAuctionNum();
        $rd['groupNum'] = $groupRs['num'];
        $rd['seckillNum'] = $seckillRs['num'];
        $rd['auctionNum'] = $auctionRs['num'];
        $rd['cuxiaoNum'] = $rd['seckillNum']+$rd['auctionNum']+$rd['groupNum']; 
        //获取优惠管理
        $m = D('Admin/Youhui');
        $youhuiRs = $m->queryPenddingYouhuiNum();
        $m = D('Admin/Ggk');
        $ggkRs = $m->queryPenddingGgkNum();
        $rd['youhuiNum'] = $youhuiRs['num'];
        $rd['ggkNum'] = $ggkRs['num'];
        $rd['discountNum'] = $rd['youhuiNum']+$rd['ggkNum'];
        //圈子管理
        $m = D('Admin/Circle');
        $circleRs = $m->queryPenddingCircleNum();
        $rd['circleListNum'] = $circleRs['num'];
        $rd['circleNum'] = $rd['circleListNum'];
        //获取提现管理
        $m = D('Admin/Top');
        $topRs = $m->queryPenddingTopNum();
        $rd['topListNum'] = $topRs['num'];
        $rd['topNum'] = $rd['topListNum'];
        //获取订单评价
        //$m = D('Admin/GoodsSun');
        //$goodsSunRs = $m->queryPenddingGoodsSunNum();
        //$rd['goodsSunNum'] = $goodsSunRs['num'];
        //获取订单投诉
        //$m = D('Admin/Complain');
        //$complainRs = $m->queryPenddingComplainNum();
        //$rd['complainNum'] = $complainRs['num'];
        //获取店铺提现
        //$m = D('Admin/Withdraw');
        //$withdrawRs = $m->queryPenddingWithdrawNum();
        //$rd['dwithdrawNum'] = $withdrawRs['num0'];
        //$rd['cwithdrawNum'] = $withdrawRs['num1'];
        //$rd['withdrawNum'] = $rd['dwithdrawNum']+$rd['cwithdrawNum'];
        $this->ajaxReturn($rd);
    }
    
    /**
     * 获取当前版本
     */
    public function getWSTMallVersion(){
    	
    	//内部开发，暂无版本监测
    	echo '内部开发，暂无版本监测';
    	exit;
    	/*
    	$this->isAjaxLogin();
    	$version = C('WST_VERSION');
    	$key = C('WST_MD5');
    	$license = $GLOBALS['CONFIG']['mallLicense'];
    	$content = file_get_contents(C('WST_WEB').'/index.php?m=Api&c=Download&a=getLastVersion&version='.$version.'&version_md5='.$key."&license=".$license."&host=".WSTDomain());
    	$json = json_decode($content,true);
    	//echo C('WST_WEB').'/index.php?m=Api&c=Download&a=getLastVersion&version='.$version.'&version_md5='.$key."&license=".$license."&host=".WSTDomain();
    	//exit;
        if($json['version'] ==  $version){
    		$json['version'] = "same";
        }
		$this->ajaxReturn($json);
		*/
    }
    
    /**
     * 输入授权码
     */
    public function enterLicense(){
    	$this->isLogin();
    	$this->display("/enter_license");
    }
    /**
     * 验证授权码
     */
    public function verifyLicense(){
    	$this->isAjaxLogin();
    	$license = I('license');
    	$content = file_get_contents(C('WST_WEB').'/index.php?m=Api&c=License&a=verifyLicense&host='.WSTRootDomain().'&license='.$license);
    	$json = json_decode($content,true);
    	$rs = array('status'=>1,'msg' => $content);
    	if($json['status']==1){
    		$rs = D('Admin/Index')->saveLicense();
    	}
    	$rs['license'] = $json;
		$this->ajaxReturn($rs);
    }
    /**
     * 清除缓存
     */
    public function cleanAllCache(){
    	$this->isAjaxLogin();
        $rv = array('status'=>-1);
		$rv['status'] = WSTDelDir(C('WST_RUNTIME_PATH'));
    	$this->ajaxReturn($rv);
    }
}