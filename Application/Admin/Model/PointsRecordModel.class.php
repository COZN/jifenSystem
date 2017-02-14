<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/9/13
 * Time: 11:47
 */

 namespace Admin\Model;
 /**
  * 积分记录类
  */
 class PointsRecordModel extends BaseModel {

     /**
      * 上传积分数据
      */
     public function importGoods($data){echo 5555;die;
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
         $goodsModel = M('PointsRecord');
         //$goodsModel = M('GoodsPlatform');
         $importNum = 0;
         //循环读取每个单元格的数据
         for ($row = 3; $row <= $rows; $row++){//行数是以第3行开始
             $goods = array();
             //$goods['id'] = 0;
             // $goods['goodsSn'] = trim($sheet->getCell("A".$row)->getValue());
            // if($goods['shopCreate']=='')break;//如果某一行第一列为空则停止导入
             if(empty($sheet->getCell("A".$row)->getValue())){
                 //如果某一行第一列为空则停止导入
                 break;
             }

             $goods['shopCreate'] = gmdate('Y-m-d H:i:s',intval(($sheet->getCell("A".$row)->getValue()-25569)*3600*24));
             $goods['member'] = trim($sheet->getCell("B".$row)->getValue());
             $goods['shopAllPoints'] = trim($sheet->getCell("C".$row)->getValue());
             $goods['kitchen'] = trim($sheet->getCell("D".$row)->getValue());
             $goods['life'] = trim($sheet->getCell("E".$row)->getValue());
             $goods['smoke'] = trim($sheet->getCell("F".$row)->getValue());
             $goods['water'] = trim($sheet->getCell("G".$row)->getValue());
             $goods['backMoney'] = trim($sheet->getCell("H".$row)->getValue());
             $goods['uploadTime'] = date('Y-m-d H:i:s');
             //查询商城分类
             /*$goodsCat = trim($sheet->getCell("G".$row)->getValue());
             $sortId=M('GoodsPlatformCats')->where(array('catId'=>$goodsCat))->getField('catId');
             $goods['shopCatId1'] = $sortId?$sortId:3;	//
             $goods['goodsStatus'] = 1;
             $goods['goodsFlag'] = 1;
             $goods['createTime'] = date('Y-m-d H:i:s');*/
             //$val = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$val);
             $readData[] = $goods;
             $importNum++;
         }

             echo '<pre/>';print_r($readData);die;

         if(count($readData)>0)$goodsModel->addAll($readData);
         return array('status'=>1,'importNum'=>$importNum);
     }
 }
 
 ?>