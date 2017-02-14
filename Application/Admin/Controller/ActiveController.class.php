<?php
 namespace Admin\Controller;
 
class ActiveController extends BaseController{
	//查看所有活动期数
	public function Index(){
		$this->isLogin();
        $m = D('Active');
        $page = $m->queryByPage();
        $pager = new \Think\Page($page['total'],$page['pageSize']);
        $page['pager'] = $pager->show();
        $this->assign('Page',$page);
    	$this->display('list');
	}

    //跳转到修改活动期数
    public function toEdit(){
        $this->isLogin();
        $m = D('Active');
        $this->checkPrivelege('hdlb_02');
        $id = (int)I('id',0);
        if($id){
            $object = $m->get($id);
        }else{
            $object = $m->getModel();
            //获取期数
            $object['activePhase'] = M('Active')->order('activePhase desc')->limit('0,1')->getField('activePhase')+1;
        }
        $this->assign('object',$object);
        $this->display('edit');
    }

    //修改活动期数
    public function edit(){
        $this->isAjaxLogin();
        $m = D('Active');
        $id = (int)I('id',0);
        if($id){
            $rs = $m->edit($id);
        }else{
            $rs = $m->insert();
        }
        $this->ajaxReturn($rs);
    }

    //删除活动
    public function del(){
        $this->isAjaxLogin();
        $this->checkPrivelege('hdlb_03');
        $m = D('Active');
        $rs = $m->del();
        $this->ajaxReturn($rs);
    }

    //锁定活动
    public function lock(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('hdlb_05');
        $m = D('Active');
        $rs = $m->lock();
        $this->ajaxReturn($rs);
    }

    //查看锁定状态
    public function getLock(){
        $this->isAjaxLogin();
        $m = D('Active');
        $rs = $m->getLock();

        $this->ajaxReturn($rs);
    }

    //查看期数对应的申请记录
    public function view(){
        $this->isLogin();
        $m = D('Active');
        $this->checkPrivelege('hdlb_01');
        $id = (int)I('id',0);
        $page = $m->getApply($id);
        $Page= new \Think\Page($page['total'],$page['pageSize'],I());
        $page['pager'] = $Page->show();
        $this->assign('Page',$page);
        $this->assign('id',$id);
        $this->assign('dataType',(int)I('dataType',-999));
        $this->assign('backStatus',(int)I('backStatus',-999));
        $this->assign('loginName',I('loginName'));
        $this->assign('referer',$_SERVER['HTTP_REFERER']);
        $this->display('apply');
    }

    /**
     * 导出数据
     */
    public function outExcel()
    {
        $this->isLogin();
        $this->checkPrivelege('fxjl_01');
        $activeModel = D('Active');
        $data = json_decode($_POST['applyId'],true);
        $activeId = intval($data['activeId']);
        $backStatus = intval($data['backStatus']);
        $exportStatus = $data['exportStatus'];
        $orderType = intval($data['orderType']);
        if($exportStatus == 0){
            //所选
            $id = trim($data['applyId'],',');
        }
        if($exportStatus ==1){
            //全部
            $arr = M('Apply')->where("isDel = 1 and applyFlag = 1")->field('applyId')->select();
            $map = array();
            foreach($arr as $v){
                $map[] =$v['applyId'];
            }
            $id = implode(',',$map);
        }
        if($exportStatus ==2){
            //查询
            $id = implode(',',unserialize(session('searchesCondition')));
        }
        $applyModel = D('Apply');
        $goods = $applyModel->getAllGoods();
        $data = array();
        $goodsId = array();
        foreach($goods as $k=>$v){
            $data[] = $v['goodsName'];
            $goodsId[] = $v['goodsId'];
        }
        $activeModels = M('Active');
        $activePhase = $activeModels->where('id = '.$activeId)->getField('activePhase');
        $array1 = array('次数','会员账号','返现金额','当次总积分','累积总积分','上传时间','备注','兑现情况');
        $array = array_merge($array1,$data);
        $list = array();
        $list = $activeModel->selectBySelecId($id,$backStatus,$activePhase,$goodsId,$orderType);
        $list = $this->handle($list);
        $this->out($list,$array,$activePhase);
    }

    /**
     * 把数据表里的数据处理并返回
     * @param $arr 从数据表里取得数据
     * @return array
     */
    public function handle($arr)
    {
        $list = array();
        foreach($arr as $key=>$val)
        {
            $list[$key] = $val;
            if(array_key_exists('dataType',$val))
            {
                if(9999 == $val['dataType']){
                    $list[$key]['dataType'] = '结束';
                }
            }
            if(array_key_exists('backStatus',$val))
            {
                if(0 == $val['backStatus']){
                    $list[$key]['backStatus'] = '待处理';
                }else if(1 == $val['backStatus']){
                    $list[$key]['backStatus'] = '已处理';
                }else if(2 == $val['backStatus']){
                    $list[$key]['backStatus'] = '已拒绝';
                }
            }
            if(array_key_exists('createTime',$val))
            {
                $list[$key]['createTime'] = date('Y-m-d H:i:s',$val['createTime']);
            }
        }
        return $list;
    }


    /**
     * 生成Excel导出数据
     * @param $list 字段内容
     * @param $array 字段名
     */
    public function out($list,$array,$activePhase)
    {
        vendor('PHPExcel');
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        $objPHPExcel->setActiveSheetIndex(0);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('Sheet1');
        $objActSheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置excel文件默认水平垂直方向居中
        $objActSheet->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        //$objActSheet->getDefaultStyle()->getFont()->setSize(14)->setName("微软雅黑");//设置默认字体大小和格式

        $objActSheet->getDefaultRowDimension()->setRowHeight(30);//设置默认行高
        $objActSheet->getColumnDimension('B')->setWidth(16);//设置宽度
        $objActSheet->getColumnDimension('C')->setWidth(13);//设置宽度
        $objActSheet->getColumnDimension('D')->setWidth(13);//设置宽度
        $objActSheet->getColumnDimension('E')->setWidth(20);//设置宽度
        $objActSheet->getColumnDimension('F')->setWidth(20);//设置宽度
        $objActSheet->getColumnDimension('G')->setWidth(20);


        $arr = $this->merge($array);

        for($i=1;$i<=count($arr);$i++) {

            $objActSheet->setCellValue($arr[$i - 1].'1', $array[$i - 1]);
        }
        $count = 1;
        $num = 0;

        foreach($list as $key=>$val)
        {
            foreach($val as $k=>$v){
                $num++;
                $number = $arr[$num - 1];
                $font = $count+1;
                $objActSheet->setCellValue($number.$font,' '.$v);
            }
            $num = 0;
            $count++;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $filename = '第'.$activePhase.'期积分返现申请';
        ob_end_clean();
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename={$filename}".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output');
    }


    /**
     * 生成A-Z之间的所有英文字母
     * @return array
     */
    public function merge($array)
    {
        $count = count($array);
        $arr = array();
        $little = 'A';
        for($i=1;$i<=$count;$i++){
                $key2 = $little++;
                $arr[] = $key2;
        }
        return $arr;
    }
      
};
?>