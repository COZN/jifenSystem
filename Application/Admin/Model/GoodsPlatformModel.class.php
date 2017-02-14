<?php
namespace Admin\Model;
use Think\Cache\Driver\Memcachesae;

/**
 * 商品分类服务类
 */
class GoodsPlatformModel extends BaseModel {
    /**
	  * 新增商品分类
	  */
	 public function insert($maxId){
	 	$rd = array('status'=>-1);
		$datas = array();
		//$data["goodsName"] = I("points_name");
		// $data["goodsName"] = $data['points_name'];

		$m = M('GoodsPlatform');
		$source = $m->group('goodsName')->select();
		 $count = count($source);
		for ($i=0;$i<$count;$i++){
			/*$v['points1'] = '';
			$v['grading_id'] = $data['id'];
			$v['create_time'] = date('Y-m-d H:i:s');*/
			unset($source[$i]['id']);
			$source[$i]['points1'] = 0;
			$source[$i]['grading_id'] = $maxId;
			$source[$i]['create_time'] = date('Y-m-d H:i:s');
		}
		 for($i=0;$i<$count;$i++){
			 $result = $m->add($source[$i]);
		 }

		//p($result);die;

		/*if($this->checkEmpty($data,true)){
			
		    $data["isShow"] = (int)I("isShow",0);
			$data["catSort"] = (int)I("catSort",0);
			$data["catFlag"] = 1;;
			$m = M('GoodsPlatformCats');

			$rs = $m->add($data);
			
			if($rs && $result){
				$rd['status']= 1;
			}
		}*/
		return $result;
	 } 
     /**
	  * 修改商品分类
	  */
	 public function edit(){
	 	$rd = array('status'=>-1);
	 	$id = (int)I("id",0);
		$data = array();
		$data["catName"] = I("catName");
	    if($this->checkEmpty($data)){
	    	$data["isShow"] = (int)I("isShow",0);
	    	$data["catSort"] = (int)I("catSort",0);
	    	$m = M('GoodsPlatformCats');
			$rs = $m->where("catFlag=1 and catId=".(int)I('id'))->save($data);
			if(false !== $rs){
				
				$rd['status']= 1;
			}
		}
		return $rd;
	 } 
	 /**
	  * 修改商品分类名称
	  */
	 public function editName(){
	 	$rd = array('status'=>-1);
	 	$id = (int)I("id",0);
		$data = array();
		$data["catName"] = I("catName");
	    if($this->checkEmpty($data)){
	    	$m = M('GoodsPlatformCats');
			$rs = $m->where("catFlag=1 and catId=".(int)I('id'))->save($data);
			if(false !== $rs){
				$rd['status']= 1;
			}
		}
		return $rd;
	 }
	 /**
	  * 获取指定对象
	  */
     public function get($id){
	 	$m = M('GoodsPlatformCats');
		return $m->where("catId=".(int)$id)->find();
	 }
	 /**
	  * 获取列表
	  */
	  public function queryByList($pid = 0){
	     $m = M('GoodsPlatformCats');
	     $rs = $m->where('catFlag=1 and parentId='.(int)$pid)->select(); 
		 return $rs;
	  }
	  /**
	   * 获取树形分类
	   */
	  public function getCatAndChild(){
	  	  $m = M('GoodsPlatformCats');
	  	  $sql = "select * from __PREFIX__goods_platform_cats where catFlag=1 order by catSort asc";
	  	  $rs1 = $m->query($sql);
	  	  return $rs1;
	  }
	 /**
	  * 删除商品分类
	  */
	 public function del(){
	 	$rd = array('status'=>-1);
		$id = (int)I('id');
	 	$m = M('GoodsPlatformCats');
	 	//把相关的商品下架了
	 	$sql = "update __PREFIX__goods set isSale=0 where shopCatId1 = {$id}";
	 	$m->execute($sql);
	 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId2 = {$id}";
	 	$m->execute($sql);
	 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId3 = {$id}";
	 	$m->execute($sql);
	 	//设置商品分类为删除状态
	 	$m->catFlag = -1;
		$rs = $m->where(" catId = {$id}")->save();
	    if(false !== $rs){
		   $rd['status']= 1;
		}
		return $rd;
	 }
	 /**
	  * 显示分类是否显示/隐藏商品分类
	  */
	 public function editiIsShow(){
	 	$rd = array('status'=>-1);
	 	if(I('id',0)==0)return $rd;
	 	$isShow = (int)I('isShow');
		$id = (int)I('id');
	 	$m = M('GoodsPlatformCats');
	 	if($isShow!=1){
	 		//把相关的商品下架了
		 	$sql = "update __PREFIX__goods set isSale=0 where shopCatId1 = {$id}";
		 	$m->execute($sql);
		 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId2 = {$id}";
		 	$m->execute($sql);
		 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId3 = {$id}";
		 	$m->execute($sql);
	 	}
	 	$m->isShow = ($isShow==1)?1:0;
	 	$rs = $m->where("catId = {$id}")->save();
	    if(false !== $rs){
			$rd['status']= 1;
		}
	 	return $rd;
	 }
	 /**
	  *新增商品
	  */
	 public function goodsInsert(){
	 	$rd = array('status'=>-1);
			$data = $_POST;
			$m = M('GoodsPlatform');
			$maxId = $this->getMaxGoodsId();
		 	$maxId =$maxId+1;		//获取商品最大ID+1等于新添加商品的ID

		 	$goodsName = $data['goodsName'];
		 	$goods_sort = $data['goods_sort'];
		 	unset($data['goodsName']);
		 	unset($data['goodsId']);
		 	unset($data['goods_sort']);
			$date = date('Y-m-d H:i:s');		//商品添加时间
		 	foreach ($data as $k=>$v){
				$k1 = $k;
				$k=substr($k,2);
				unset($data[$k1]);
				$data[$k] = $v;
			}

		 $rs = array();
		 foreach($data as $k=>$v){
			 $sql = "INSERT INTO oto_goods_platform( `goodsId`, `goodsName`, `points1`, `grading_id`, `create_time`, `goods_sort`) VALUES ($maxId,'$goodsName','$v'/100,'$k','$date','$goods_sort')";
			 $rs = $m->execute($sql);
		 }
			if($rs){
				$rd['status']= 1;
			}
		return $rd;
	 } 
	 /**
	  * 分页平台商品列表
	  */
     public function queryByPage(){
        $m = M('GoodsPlatform');
		$p = M('GoodsPointsPart');
		$cp = $p->count();
     	$goodsName = I('goodsName');
		$sql = "select * from __PREFIX__goods_platform WHERE isdel != 0 ";
		if($goodsName!='')$sql.=" and goodsName LIKE '%".$goodsName."%'";
		$sql.="ORDER BY goods_sort,id ASC ";
		$pageSize = $cp*20;
		$rs = $m->pageQuery($sql,'',$pageSize);
		return $rs;
	 }
	 /*
	  *获取指定商品
	  */
	 public function gets($id){
	 	$m = M('GoodsPlatform');
	 	$rs = $m->where("goodsId = $id")->order('grading_id asc')->select();
		$data=array();
		 foreach ($rs as $v){
			 $data['id'] = $v['id'];
			 $data['goodsId'] = $v['goodsId'];
			 $data['goodsName'] = $v['goodsName'];
			 $data['p'][] = $v['points1']*100;
			 $data['goods_sort'] = $v['goods_sort'];
		 }
	 	return $data;
	 }
     /**
	  * 修改商品
	  */
	 public function goodsEdit($count){
	 	$rd = array('status'=>-1);
		 $m = M('GoodsPlatform');
		 $data = $_POST;
		 $goodsId = $data['goodsId'];
		 $goodsName = $data['goodsName'];
		 $goods_sort = $data['goods_sort']?$data['goods_sort']:0;
		 $data= array_slice($data,2);
		 foreach ($data as $k=>$v){
			 $gradingId = substr($k,2);
			 $sql = "update __PREFIX__goods_platform set points1=$v/100, goodsName='$goodsName',goods_sort=$goods_sort where goodsId=$goodsId AND grading_id=$gradingId";
			 $rs = $m->query($sql);
		 }
         $rd['status']= 1;
		return $rd;
	 } 
	 /**
	  * 删除商品
	  */
	 public function delGoods(){
	 	$m = M("GoodsPlatform");
	 	$rd = array('status'=>-1);
		$id = (int)I('id');
	 	//设置商品分类为删除状态
	 	//$m->isdel = 0;
		//$rs = $m->where(" goodsId = {$id}")->save();
		$rs = $m->where(" goodsId = {$id}")->delete();
	    if(false !== $rs){
		   $rd['status']= 1;
		}
		return $rd;
	 }
	 /**
	  * 显示商品是否同步
	  */
	 public function editiIsSynchro(){
	 	$rd = array('status'=>-1);
	 	if(I('id',0)==0)return $rd;
	 	$isShow = (int)I('isShow');
		$id = (int)I('id');
	 	$m = M('GoodsPlatform');
	 	if($isShow!=1){
	 		//把相关的商品下架了
		 	$sql = "update __PREFIX__goods set isSale=0 where shopCatId1 = {$id}";
		 	$m->execute($sql);
		 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId2 = {$id}";
		 	$m->execute($sql);
		 	$sql = "update __PREFIX__goods set isSale=0 where goodsCatId3 = {$id}";
		 	$m->execute($sql);
	 	}
	 	$m->isSynchro = ($isShow==1)?1:0;
	 	$rs = $m->where("goodsId = {$id}")->save();
	    if(false !== $rs){
			$rd['status']= 1;
		}
	 	return $rd;
	 }

	/**
	 * 上传商品数据
	 */
	public function importGoods($data){
		$objReader = WSTReadExcel($data['file']['savepath'].$data['file']['savename']);
        $objReader->setActiveSheetIndex(0); 
        $sheet = $objReader->getActiveSheet();
        $rows = $sheet->getHighestRow();
        $cells = $sheet->getHighestColumn();
        //数据集合
        $readData = array();
        $goodsCatMap = array();
        $shopGoodsCatMap = array();
        $brandMap = array();
        $shopId = (int)session('WST_USER.shopId');
        $goodsModel = M('GoodsPlatform');
        $importNum = 0;
        //循环读取每个单元格的数据
        for ($row = 3; $row <= $rows; $row++){//行数是以第3行开始
            $goods = array();
            $goods['shopId'] = 0;
           // $goods['goodsSn'] = trim($sheet->getCell("A".$row)->getValue());
			$goods['goodsName'] = trim($sheet->getCell("A".$row)->getValue());
            if($goods['goodsName']=='')break;//如果某一行第一列为空则停止导入
           /* $goods['goodsName'] = trim($sheet->getCell("B".$row)->getValue());
            $goods['marketPrice'] = trim($sheet->getCell("C".$row)->getValue());
            $goods['shopPrice'] = trim($sheet->getCell("D".$row)->getValue());
            $goods['goodsStock'] = trim($sheet->getCell("E".$row)->getValue());
            $goods['goodsUnit'] = trim($sheet->getCell("F".$row)->getValue());*/
			$goods['points1'] = trim($sheet->getCell("B".$row)->getValue());
			$goods['points2'] = trim($sheet->getCell("C".$row)->getValue());
			$goods['points3'] = trim($sheet->getCell("D".$row)->getValue());
			$goods['points4'] = trim($sheet->getCell("E".$row)->getValue());
			$goods['points5'] = trim($sheet->getCell("F".$row)->getValue());
            //查询商城分类
            $goodsCat = trim($sheet->getCell("G".$row)->getValue());
			$sortId=M('GoodsPlatformCats')->where(array('catId'=>$goodsCat))->getField('catId');
            $goods['shopCatId1'] = $sortId?$sortId:3;	//
            $goods['goodsStatus'] = 1;
            $goods['goodsFlag'] = 1;
            $goods['createTime'] = date('Y-m-d H:i:s');
            //$val = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$val);
            $readData[] = $goods;
            $importNum++;
        }		//echo '<pre/>';print_r($readData);die;
        if(count($readData)>0)$goodsModel->addAll($readData);
        return array('status'=>1,'importNum'=>$importNum);
	}

	/**
	 *批量删除
     */
    public function BatchDelete($id)
    {

    	$m = M('GoodsPlatform');
        $map['goodsId'] = array('in',$id);
        return $m->where($map)->setField('isdel',0);
    }
	/**
	 *同步
     */
    public function BatchSynchro($id)
    {
    	$status = intval(I('post.status'));
    	$m = M('GoodsPlatform');
        $map['goodsId'] = array('in',$id);
        return $m->where($map)->setField('isSynchro',$status);
    }

	/**
	 * 获取所有类目
	 */
	public function getAll(){
		$m = M('GoodsPlatform');
		$sql = "select * from __PREFIX__goods_platform  group by points_name";
		$rs1 = $m->query($sql);

	}

	/**
	 * 统计商品分组后的条数
	 * @return mixed
	 */
	public function getGoodsId(){
		$m = M('GoodsPlatform');
		$res = $m->field('goodsId')->where('isdel=1')->group('goodsId')->select();
		return $res;
	}
	public function getMaxGoodsId(){
		$maxId = M('GoodsPlatform')->max('goodsId');
		return $maxId;
	}
};
?>