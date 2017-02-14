<?php
 namespace Admin\Controller;
 
class ApplyController extends BaseController{
	/**
	 * 跳到Excel导入返现申请页
	 */
	public function import(){
		$this->isLogin();
        //获取活动
        $m = D('Active');
        $activeData = $m->getAllActiveNotLock();
        $this->assign('activeData',$activeData);
        if(1 == count($activeData)){
            $activeNum = 0;
        }else{
            $activeNum = 1;
        }
        $this->assign('activeNum',$activeNum);
    	$this->display('import');
	}
	/**
     * 上传积分返现申请数据
     */
    public function importGoods(){
        $startTime = time();
    	$config = array(
		        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
		        'exts'          =>  array('xls','xlsx','xlsm'), //允许上传的文件后缀
		        'rootPath'      =>  './Upload/', //保存根路径
		        'driver'        =>  'LOCAL', // 文件上传驱动
		        'subName'       =>  array('date', 'Y-m'),
		        'savePath'      =>  I('dir','uploads')."/"
		);
		$upload = new \Think\Upload($config);
		$rs = $upload->upload($_FILES);
		$rv = array('status'=>-1);
		if(!$rs){
			$rv['msg'] = $upload->getError();
		}else{
			$m = D('Admin/Apply');
    	    $rv = $m->importGoods($rs);
            $rv['time'] = time() - $startTime;
		}
    	$this->ajaxReturn($rv);
    }

    //跳转到手写输入页面
    public function handInput(){
        $this->isLogin();
        $this->checkPrivelege('sxsr_00');
        $m = D('Active');
        $activeData = $m->getAllActiveNotLock();
        $this->assign('activeData',$activeData);
        $this->assign('activeData',$activeData);
        if(1 == count($activeData)){
            $activeNum = 0;
        }else{
            $activeNum = 1;
        }
        $this->assign('activeNum',$activeNum);
        $goodsModel = D('Apply');
        $goods = $goodsModel->getAllGoods();
        $this->assign('goods',$goods);
        $goodsId = array();
        foreach($goods as $k=>$v){
            $goodsId[] = $v['goodsId'];
        }
        $this->assign('goodsId',$goodsId);
        $this->display('handInput');
    }

    //手写输入上传
    public function handImport(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('sxsr_00');
        $m = D('Apply');
        $rs = $m->handImport();
        $this->ajaxReturn($rs);
    }


    //查看申请记录
    public function record(){
    	$this->isLogin();
    	$m = D('Apply');
    	$page = $m->queryByPage();
    	$pager = new \Think\Page($page['total'],$page['pageSize'],I());
    	$page['pager'] = $pager->show();
        $activeModel = M('Active');
        //获取所有活动期数
        $allActive = $activeModel->field('activePhase')->order('activePhase desc')->select();
        $this->assign('allActive',$allActive);
        //获取最新一期
        $newActivePhase = $activeModel->where('lockStatus = 0')->order('activePhase desc')->limit('0,1')->getField('activePhase');
        //所有活动都锁定，就不管是否锁定了
        if(0 == $newActivePhase){
            $newActivePhase = $activeModel->order('activePhase desc')->limit('0,1')->getField('activePhase');
        }
        $this->assign('activePhase',(int)I('activePhase',$newActivePhase));
        //获取期数对应的id
        $activeId = $activeModel->where('activePhase = '.(int)I('activePhase',$newActivePhase))->getField('id');
        $this->assign('activeId',$activeId);
        $this->assign('dataType',(int)I('dataType',-999));
        $this->assign('backStatus',(int)I('backStatus',-999));
        $this->assign('loginName',I('loginName'));
        $this->assign('orderType',I('orderType',0));
        $this->assign('Page',$page);
    	$this->display('record');
    }

    //修改提现状态
	public function editBackStatus(){
	 	$this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxjl_04');
	 	$m = D('Admin/Apply');
		$rs = $m->editBackStatus();
		$this->ajaxReturn($rs);
	}

	//批量修改提现状态
    public function  changeBackStatus(){
    	$this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxjl_04');
        $res = D('Apply')->changeBackStatus();
        $this->ajaxReturn($res);
    }

    //跳转到编辑备注
    public function toEditRemark(){
    	$this->isAjaxLogin();
        $this->checkPrivelege('fxjl_03');
    	$m = D('Apply');
    	$object = $m->getDetails();
    	$this->assign('object',$object);
    	$this->assign('referer',$_SERVER['HTTP_REFERER']);
    	$this->display('remark');
    }

    //修改备注
    public function editRemark(){
    	$this->isAjaxLogin();
    	$m = D('Apply');
    	$rs = $m->editRemark();
    	$this->ajaxReturn($rs);
    }

    //查看申请商品
    public function view(){
    	$this->isAjaxLogin();
        $this->checkPrivelege('fxjl_02');
    	$m = D('Apply');
    	$rs = $m->view();
    	$this->assign('Page',$rs);
        $this->assign('referer',$_SERVER['HTTP_REFERER']);
    	$this->display('view');
    }

    //会员账号
    public function users(){
        $this->isLogin();
        $this->checkPrivelege('hyzh_00');
        $m = D('Apply');
        $page = $m->getUsers();
        $pager = new \Think\Page($page['total'],$page['pageSize']);
        $page['pager'] = $pager->show();
        $this->assign('Page',$page);
        $this->display('users');
    }

    //返回最近一次上传之前
    public function backLast(){
        $this->isAjaxLogin();
        $m = D('Apply');
        $rs = $m->backLast();
        $this->ajaxReturn($rs);
    }

    //批量删除申请记录
    public function BatchDelete(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxjl_05');
        $m = D('Apply');
        $rs = $m->BatchDelete();
        $this->ajaxReturn($rs);
    }

    //批量删除活动期数内的申请记录
    public function BatchDeleteActive(){
        $this->isAjaxLogin();
        $page = (int)I('page',0);
        if(0 == $page){
            $this->checkAjaxPrivelege('hdlb_06');
        }else if(1 == $page){
            $this->checkAjaxPrivelege('hdlb_07');
        }else if(2 == $page){
            $this->checkAjaxPrivelege('hdlb_08');
        }
        $m = D('Apply');
        $rs = $m->BatchDeleteActive();
        $this->ajaxReturn($rs);
    }
};
?>