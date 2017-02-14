<?php
 namespace Admin\Controller;
/**
 * 平台商品分类控制器
 */
class GoodsPlatformController extends BaseController{
	/**
	 * 跳到新增/编辑商品类型页面
	 */
	public function toEdit(){
		$this->isLogin();
	    $m = D('Admin/GoodsPlatform');
    	$object = array();
    	if(I('id',0)>0){
    	
    		$object = $m->get(I('id',0));
    	}else{
    	
    	
    		   $object = $m->getModel();
    	
    	}
    	$this->assign('object',$object);
		$this->view->display('/GoodsPlatform/edit');
	}
	/**
	 * 新增/修改商品类型操作
	 */
	public function edit(){
		$this->isAjaxLogin();
		$m = D('Admin/GoodsPlatform');
    	$rs = array();
    	if(I('id',0)>0){
    		
    		$rs = $m->edit();
    	}else{
    		
    		$rs = $m->insert();
    	}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 修改商品类型名称
	 */
	public function editName(){
		$this->isAjaxLogin();
		$m = D('Admin/GoodsPlatform');
    	$rs = array('status'=>-1);
    	if(I('id',0)>0){
    		$this->checkAjaxPrivelege('spfl_02');
    		$rs = $m->editName();
    	}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 删除商品类型操作
	 */
	public function del(){
		$this->isAjaxLogin();
		
		$m = D('Admin/GoodsPlatform');
    	$rs = $m->del();
    	$this->ajaxReturn($rs);
	}
	/**
	 * 分页查询商品类型
	 */
	public function index(){
		$m = D('Admin/GoodsPlatform');
    	$list = $m->getCatAndChild();
    	$this->assign('List',$list);
        $this->display("/GoodsPlatform/list");
	}
	/**
	 * 分页查询积分段类型
	 */
	public function pointsPart(){
		$m = D('Admin/PointsPart');
		$list = $m->getCatAndChild();
		$this->assign('List',$list);
		$this->display("/GoodsPlatform/points");
	}
	/**
	 * 列表查询商品类型
	 */
    public function queryByList(){
    	$this->isAjaxLogin();
		$m = D('Admin/GoodsPlatform');
		$list = $m->queryByList(I('id'));
		$rs = array();
		$rs['status'] = 1;
		$rs['list'] = $list;
		$this->ajaxReturn($rs);
	}
    /**
	 * 显示商品类型是否显示/隐藏
	 */
	 public function editiIsShow(){
	 	$this->isAjaxLogin();
	
	 	$m = D('Admin/GoodsPlatform');
		$rs = $m->editiIsShow();
		$this->ajaxReturn($rs);
	 }
	 /**
	  *跳转到新增/编辑商品
	  */
	 public function toAdd(){
	 	$m = D('Admin/GoodsPlatform');
		$p = D('Admin/GoodsPointsPart');
		$list = $p->getCatAndChild();
		 $this->checkPrivelege('splb_02');
	 	if(I('id',0)>0){
			$this->checkPrivelege('splb_03');
	 		$object = $m->gets(I('id',0));
	 	}
		// p($list);die;
		 $count = count($list);
		 $pointsValue = array_slice($object,-($count));	//取出积分段的值
	 	//$object['cats'] = $m->getCatAndChild();
		//$object['cats'] = $p->getCatAndChild();
		
	 	$this->assign('object',$object);
		$this->assign('list',$list);
		//p($list);die;
	 	$this->display('/GoodsPlatform/add');
	 }
	 /**
	  *新增/编辑商品操作
	  */
	 public function add(){
	 	$m = D('Admin/GoodsPlatform');
		$p = D('Admin/GoodsPointsPart');
		$count = count($p->getCatAndChild());
		if(!$_POST['goodsName'] || !isset($_POST['goodsName'])){
			$data = array(
				'status'=>0,
				'content'=>'商品名不能为空',
			);
			exit(json_encode($data));
		}
	 	if(I('goodsId',0)>0){
	 		$rs = $m->goodsEdit($count);
	 	}else{
	 		$rs = $m->goodsInsert();
	 	}	
		$this->ajaxReturn($rs);
	 }

	/**
	 * 分页查询平台商品
	 */
	public function goods(){
		$this->isLogin();
		$shopCatId = I('shopCatId1');
		//获取商品分类信息
		$m = D('Admin/GoodsPlatform');
		$p = D('Admin/GoodsPointsPart');

		$shopCatId1 = $m->getCatAndChild();
		$pointsPart = $p->getCatAndChild();	//获取积分段分类
																//$count = count($pointsPart);
		$goodsIdArray = $m->getGoodsId();
		$count = count($goodsIdArray);
		$this->assign('shopCatId1',$shopCatId1);
		$this->assign('catList',$pointsPart);

		$this->assign('shopCatId',$shopCatId);

		//$m = D('Admin/GoodsPlatform');
    	$page = $m->queryByPage();


		$data = array();
		foreach ($page['root'] as $k=>$v){
			for($i=0;$i<$count;$i++){
				if($v['goodsId'] == $goodsIdArray[$i]['goodsId']){
					$data[$i]['goodsId'] = $v['goodsId'];
					$data[$i]['goodsName'] = $v['goodsName'];
					$data[$i]['p'][] = $v['points1'];
				}
			}
		}
		$page['root'] = $data;
				//p($page);die;
    	$pager = new \Think\Page($page['total'],$page['pageSize']);// 实例化分页类 传入总记录数和每页显示的记录数
		
    	$page['pager'] = $pager->show();
    	$this->assign('Page',$page);
		//p($page['root']);die;

		$this->display("/GoodsPlatform/lists");
	}
	/**
	 * 删除商品操作
	 */
	public function delGoods(){
		$this->isAjaxLogin();
		$this->checkAjaxPrivelege('splb_04');
		$m = D('Admin/GoodsPlatform');
    	$rs = $m->delGoods();
    	$this->ajaxReturn($rs);
	}
	/**
	 * 显示商品是否同步
	 */
	 public function editiIsSynchro(){
	 	$this->isAjaxLogin();
	 	$m = D('Admin/GoodsPlatform');
		$rs = $m->editiIsSynchro();
		$this->ajaxReturn($rs);
	 }
	 /**
	 * 数据导入
	 */
    public function import(){
    	$this->isAjaxLogin();
    	$this->display('/GoodsPlatform/import');
	}

	/**
     * 上传商品数据
     */
    public function importGoods(){
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
			$m = D('Admin/GoodsPlatform');
    	    $rv = $m->importGoods($rs);
		}
    	$this->ajaxReturn($rv);
    }
    /**
     * 批量删除
     */
    public function deletes()
    {
        $this->isLogin();
        $disposable = D('Admin/GoodsPlatform');
        $id = trim(I('post.id','','htmlspecialchars'),',');
        $info =array();
        $disposable->BatchDelete($id)? $info['status'] = 1 :$info['status'] = 0;
        $this->ajaxReturn($info);
        exit;
    }
    /**
     * 批量同步
     */
    public function synchros()
    {
        $this->isLogin();
        $disposable = D('Admin/GoodsPlatform');
        $id = trim(I('post.id','','htmlspecialchars'),',');
        $info =array();
        $disposable->BatchSynchro($id)? $info['status'] = 1 :$info['status'] = 0;
        $this->ajaxReturn($info);
        exit;
    }
};
?>