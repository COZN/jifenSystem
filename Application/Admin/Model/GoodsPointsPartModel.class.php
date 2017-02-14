<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/9/14
 * Time: 9:48
 */

namespace Admin\Model;
/**
 * 积分段分类模型
 */
class GoodsPointsPartModel extends BaseModel {

    
    /**
     * 新增商品分类
     */
    public function insert(){
        $rd = array('status'=>-1);
        $data = array();
        $data["points_name"] = I("points_name");
        $data["points_value"] = I("points_value");
       
        if($this->checkEmpty($data,true)){

            //$data["catSort"] = (int)I("catSort",0);
            //$data["catFlag"] = 1;;
            $m = M('GoodsPointsPart');
            
            $rs = $m->add($data);
            
            if($rs){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 修改商品分类
     */
    public function edit(){
        $rd = array('status'=>-1);
        $id = (int)I("id",0);
        $data = array();
        $data['points_name'] = I("points_name");
        $data['points_value'] = I("points_value");
        if($this->checkEmpty($data)){
            //$data["is_show"] = (int)I("is_show",0);
            //$data["catSort"] = (int)I("catSort",0);
            $m = M('GoodsPointsPart');

            $rs = $m->where(" id=".(int)I('id'))->save($data);
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
        if($_POST['name'] =='points_name'){
            $data['points_name'] = $_POST['parameter'];
        }
        if($_POST['name'] =='points_value'){
            $data['points_value'] = $_POST['parameter'];
        }
        //p($data);die;
        if($this->checkEmpty($data)){
            $m = M('GoodsPointsPart');
            $rs = $m->where("id=".(int)I('id'))->save($data);
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
        $m = M('GoodsPointsPart');
        return $m->where("id=".(int)$id)->find();
    }

    /**
     * 获取最大ID值
     * @return mixed
     */
    public function getMaxId(){
        $m = M('GoodsPointsPart');
        return $m->max('id');
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
        //$sql = "select * from __PREFIX__goods_platform_cats where catFlag=1 order by catSort asc";
        $sql = "select * from __PREFIX__goods_points_part where is_show=1 order by id asc";
        $rs1 = $m->query($sql);
        //p($rs1);die;
        return $rs1;
    }

    /**
     * 删除商品分类
     */
    public function del(){
        $rd = array('status'=>-1);
        $id = (int)I('id');
        $m = M('GoodsPointsPart');
        $p=M('GoodsPlatform');
        //把相关的商品下架了
        /*$sql = "update __PREFIX__goods set isSale=0 where shopCatId1 = {$id}";
        $m->execute($sql);
        $sql = "update __PREFIX__goods set isSale=0 where goodsCatId2 = {$id}";
        $m->execute($sql);*/
        //$sql = "update __PREFIX__goods_platform set isdel=0 where grading_id = {$id}";
        $sql = "delete from __PREFIX__goods_platform  where grading_id = {$id}";
        $p->execute($sql);
        //设置商品分类为删除状态
        //$m->is_show = 0;
        
        $rs = $m->where(" id = {$id}")->delete();
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
        $isShow = (int)I('is_show');
        $id = (int)I('id');
        $m = M('GoodsPointsPart');
        if($isShow!=1){
            //把相关的商品下架了
            $sql = "update __PREFIX__goods set isSale=0 where shopCatId1 = {$id}";
            $m->execute($sql);
            $sql = "update __PREFIX__goods set isSale=0 where goodsCatId2 = {$id}";
            $m->execute($sql);
            $sql = "update __PREFIX__goods set isSale=0 where goodsCatId3 = {$id}";
            $m->execute($sql);
        }
        $m->is_show = ($isShow==1)?1:0;
        $rs = $m->where("id = {$id}")->save();
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
        $data = array();
        $data["shopId"] = 0;
        $data["goodsSn"] = I("goodsSn");
        $data["goodsName"] = I("goodsName");
        $data["goodsImg"] = I("goodsImg");
        $data["goodsThums"] = I("goodsThums");
        $data["marketPrice"] = (float)I("marketPrice");
        $data["shopPrice"] = (float)I("shopPrice");
        $data["goodsStock"] = (int)I("goodsStock");
        $data["goodsUnit"] = I("goodsUnit");
        $data["isSale"] = 1;
        $data["shopCatId1"] = (int)I("catsId");
        $data["isShopRecomm"] = 0;
        $data["isIndexRecomm"] = 0;
        $data["isActivityRecomm"] = 0;
        $data["isInnerRecomm"] = 0;
        $data['goodsStatus'] = 1;
        $data["goodsFlag"] = 1;
        $data["createTime"] = date('Y-m-d H:i:s');
        $data["brandId"] = 0;
        $data["goodsSpec"] = I("goodsSpec");
        $data["goodsKeywords"] = I("goodsKeywords");
        $m = M('GoodsPlatform');
        $rs = $m->data($data)->add();
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
        $goodsName = I('goodsName');
        $goodsSn = I('goodsSn');
        $shopCatId1 = I('shopCatId1');
        $sql = "select g.*,gc.catName from __PREFIX__goods_platform as g 
	 	      inner join __PREFIX__goods_platform_cats as gc on g.shopCatId1=gc.catId 
	 	      where g.isSale=1 and g.shopId=0 and g.goodsStatus=1 and isdel=1";
        if($goodsName!='')$sql.=" and g.goodsName like '%".$goodsName."%'";
        if($goodsSn!='')$sql.=" and g.goodsSn like '%".$goodsSn."%'";
        if($shopCatId1!=0)$sql.=" and g.shopCatId1 =".$shopCatId1;
        $sql.="  order by goodsId desc";
        $rs = $m->pageQuery($sql);
        return $rs;
    }
    /*
     *获取指定商品
     */
    public function gets($id){
        $m = M('GoodsPlatform');
        $rs = $m->where("goodsId = $id")->find();
        return $rs;
    }
    /**
     * 修改商品
     */
    public function goodsEdit(){
        $rd = array('status'=>-1);
        $data = array();
        $data["shopId"] = 0;
        $data["goodsSn"] = I("goodsSn");
        $data["goodsName"] = I("goodsName");
        $data["goodsImg"] = I("goodsImg");
        $data["goodsThums"] = I("goodsThums");
        $data["marketPrice"] = (float)I("marketPrice");
        $data["shopPrice"] = (float)I("shopPrice");
        $data["goodsStock"] = (int)I("goodsStock");
        $data["goodsUnit"] = I("goodsUnit");
        $data["shopCatId1"] = (int)I("catsId");
        $data["isShopRecomm"] = 0;
        $data["isIndexRecomm"] = 0;
        $data["isActivityRecomm"] = 0;
        $data["isInnerRecomm"] = 0;
        $data['goodsStatus'] = 1;
        $data["goodsFlag"] = 1;
        $data["createTime"] = date('Y-m-d H:i:s');
        $data["brandId"] = 0;
        $data["goodsSpec"] = I("goodsSpec");
        $data["goodsKeywords"] = I("goodsKeywords");
        $m = M('GoodsPlatform');
        $rs = $m->where("goodsId=".(int)I('goodsId'))->save($data);
        if(false !== $rs){
            $rd['status']= 1;
        }
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
        $m->isdel = 0;
        $rs = $m->where(" goodsId = {$id}")->save();
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
            $goods['goodsSn'] = trim($sheet->getCell("A".$row)->getValue());
            if($goods['goodsSn']=='')break;//如果某一行第一列为空则停止导入
            $goods['goodsName'] = trim($sheet->getCell("B".$row)->getValue());
            $goods['marketPrice'] = trim($sheet->getCell("C".$row)->getValue());
            $goods['shopPrice'] = trim($sheet->getCell("D".$row)->getValue());
            $goods['goodsStock'] = trim($sheet->getCell("E".$row)->getValue());
            $goods['goodsUnit'] = trim($sheet->getCell("F".$row)->getValue());
            //查询商城分类
            $goodsCat = trim($sheet->getCell("G".$row)->getValue());
            $sortId=M('GoodsPlatformCats')->where(array('catId'=>$goodsCat))->getField('catId');
            $goods['shopCatId1'] = $sortId?$sortId:0;
            $goods['goodsStatus'] = 1;
            $goods['goodsFlag'] = 1;
            $goods['createTime'] = date('Y-m-d H:i:s');
            //$val = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$val);
            $readData[] = $goods;
            $importNum++;
        }print_r($readData);
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
    
}

?>