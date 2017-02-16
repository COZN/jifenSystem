<?php
 namespace Admin\Model;

class ApplyModel extends BaseModel {
	/**
	 * 上传积分返现申请数据
	 */
	public function importGoods($data){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $time = time();
		$objReader = WSTReadExcel($data['file']['savepath'].$data['file']['savename']);
        $objReader->setActiveSheetIndex(0); 
        $sheet = $objReader->getActiveSheet();
        $rows = $sheet->getHighestRow();
        $cells = $sheet->getHighestColumn();
        $rd = array('status'=>-1);
        $activePhase = (int)I('activePhase',0);
        $dataType = (int)I('dataType',0);
        $userModel = M('Users');
        $applyModel = M('Apply');
        $applyLimitModel = M('ApplyLimit');
        $importNum = 0;
        //黑名单用户
        $blackName = array();
        //数据出错用户
        $wrongName = array();
        //循环读取每个单元格的数据
        for ($row = 2; $row <= $rows; $row++){//行数是以第3行开始
            $goodsFlag = true;
            $applyGoods = array();
            $sumScore = 0;
            $money = 0;
            $loginName = trim($sheet->getCell('A'.$row)->getValue());
            if($loginName=='')break;//如果某一行第一列为空则停止导入
            //获取会员id
            $userData = $userModel->where("loginName = '".$loginName."'")->field('userId,blacklist,limit_id')->find();
            //判断会员是否被拉黑
            if($dataType == 1){
                if($userData['blacklist'] == 1){$blackName[] = $loginName;continue;}
            }
            $userId = $userData['userId'];
            //获取该会员该期最后一次申请记录
            $lastApplyData = $applyModel->where('isDel = 1 and activePhase = '.$activePhase.' and dataType > 0 and dataType != 9999'.' and userId = '.$userId)->order('applyId desc')->field('activeTotalScore,dataType,goodsScore')->find();
            //循环列计算上传总积分
            $little = 'A';
            $allLitte = '';
            for($i=1;$i<23333;$i++){
                $allLitte = $little++;
                if($allLitte != 'A'){
                    $goodsName = trim($sheet->getCell($allLitte.'1')->getValue());
                    if($goodsName == '')break;//如果没有了商品则停止循环
                    $totalGoodsScore = trim($sheet->getCell($allLitte.$row)->getValue());
                    $sumScore += $totalGoodsScore;
                }
            }
            //本次申请总积分=上传总积分-最后一次上传总积分
            $totalScore = "$sumScore" - $lastApplyData['activeTotalScore'];
            if(0 > $totalScore){$wrongName[] = $loginName;continue;}
            $pointId = M('GoodsPointsPart')->where('points_value <= '.$totalScore)->order('points_value desc')->getField('id');
            $goodsScoreData = json_decode($lastApplyData['goodsScore']);
            //循环列计算每种商品返现比例
            $little = 'A';
            $allLitte = '';
            
            for($i=1;$i<23333;$i++){
            	$allLitte = $little++;
            	if($allLitte != 'A'){
            		$totalGoodsScore = trim($sheet->getCell($allLitte.$row)->getValue());
            		$goodsName = trim($sheet->getCell($allLitte.'1')->getValue());
                    if($goodsName == '')break;//如果没有了商品则停止循环
            		//获取该商品的返现金额
                    if(0 < $pointId){
                        $where = "goodsName = '".$goodsName."' and grading_id = ".$pointId;
                    }else{
                        $where = "goodsName = '".$goodsName."'";
                    }
            		$goodsData = M('GoodsPlatform')->where($where)->field('goodsId,points1')->find();
                    $goodsId = $goodsData['goodsId'];
                    $proportion = $pointId>0?$goodsData['points1']:0;
                    //本次该商品申请积分=上传商品积分-最后一次上传商品积分
                    $lastScore = 0;
                    foreach($goodsScoreData as $ks=>$vs){
                        $goods = explode('-',$vs);
                        if($goodsId == $goods[0]){
                            $lastScore = $goods[1];
                        }
                    }
                    $goodsScore = $totalGoodsScore - $lastScore;
                    //积分不能为负数
                    if($goodsScore < 0){
                        $goodsName = M('GoodsPlatform')->where('goodsId = '.$goodsId)->getField('goodsName');
                        $wrongName[] = $loginName;
                        $goodsFlag = false;
                        break;
                    }
                    $applyGoods[] = $goodsId.'-'.$totalGoodsScore.'-'.$proportion;
            		$money += $proportion*$goodsScore;
            	}
            }
            if(false == $goodsFlag){continue;}
            //增加会员
            if(!$userId){
                $data = array();
                $data['loginName'] = $loginName;
                $data['createTime'] = $time;
                $data['limit_id'] = $applyLimitModel->where('is_del = 0 and limit_flag = 1')->getField('id');
                $userId = $userModel->add($data);
                $userData['limit_id'] = $data['limit_id'];
            }
            //获取返现上限
            $limit_value = $applyLimitModel->where('id = '.$userData['limit_id'].' and is_del = 0')->getField('limit_value');
            //获取累积提现金额
            $sumCashBack = $applyModel->where('userId = '.$userId.' and activePhase = '.$activePhase)->sum('cashBack');
            $money = ($limit_value-$sumCashBack)>$money?$money:($limit_value-$sumCashBack);
    		//增加提现申请
            if($userId){
                $apply = array();
                $apply['userId'] = $userId;
                $apply['activeTotalScore'] = $sumScore;
                $apply['totalScore'] = $totalScore;
                $apply['cashBack'] = $money;
                $apply['createTime'] = $time;
                $apply['activePhase'] = $activePhase;
                $apply['isDel'] = 1;
                if(($dataType == 1) && ($lastApplyData['dataType'] > 0) && ($lastApplyData['dataType'] != 9999) ){
                    $apply['dataType'] = $lastApplyData['dataType']+1;
                }else{
                    $apply['dataType'] = $dataType;    
                }
                $apply['goodsScore'] = json_encode($applyGoods);
                $applyId = $applyModel->add($apply);
            }
            $importNum++;
            if($applyId ){
                $rd['status'] = 1;
            }
            unset($apply);
        }
        unset($row);unset($sheet);
        $rd['importNum'] = $importNum;
        $rd['blackName'] = implode(',',$blackName);
        $rd['wrongName'] = implode(',',$wrongName);
        return $rd;
	}

    //获取申请积分返现记录
    public function queryByPage(){
        set_time_limit(0);
        $m = M('Apply');
        //获取最新且未锁定期数
        $activeModel = M('Active');
        $newActivePhase = $activeModel->where('lockStatus = 0')->order('activePhase desc')->limit('0,1')->getField('activePhase');
        //所有活动都锁定，就不管是否锁定了
        if(0 == $newActivePhase){
            $newActivePhase = $activeModel->order('activePhase desc')->limit('0,1')->getField('activePhase');
        }
        $activePhase = (int)I('activePhase',$newActivePhase);
        $backStatus = (int)I('backStatus',-999);
        $loginName = I('loginName');
        $orderType = (int)I('orderType',0);
        $dataType = (int)I('dataType',-999);
        $sql = "select a.applyId,a.totalScore,a.cashBack,a.backStatus,activePhase,a.dataType,a.activeTotalScore,a.createTime,u.loginName,a.remark from __PREFIX__apply a left join __PREFIX__users u on u.userId = a.userId where a.isDel = 1 and a.applyFlag = 1";
        if($activePhase>0){$sql .= " and a.activePhase = ".$activePhase;}
        if($backStatus!=-999){$sql .= " and a.backStatus = ".$backStatus;}
        if($loginName!=''){$sql .=" and u.loginName = '".$loginName."'";}
        if($dataType!=-999){$sql .= " and a.dataType = ".$dataType;}
        if(0 == $orderType){
            $sql .= " order by a.createTime desc ";
        }else if(1 == $orderType){
            $sql .= " order by a.activePhase,a.userId,a.dataType";
        }
        $page = $m->pageQuery($sql);
        $arr = $m->query($sql);
        $map = array();
        foreach($arr as $v){
            $map[] =$v['applyId'];
        }
        //由于结束dataType=9999,所以获取所有申请次数
        $allDataType = $m->where('activePhase = '.$activePhase.' and isDel = 1 and applyFlag = 1')->field('dataType')->select();
        $data = array();
        foreach($allDataType as $k=>$v){
            if(!in_array($v['dataType'],$data) && 9999 != $v['dataType']){
                $data[] = $v['dataType'];
            }
        }
        $page['allDataType'] = $data;
        //将查询到的id存储到session中，方便导出数据
        session('searchesCondition',serialize($map));
        return $page;
    }

    //修改提现状态
    public function editBackStatus(){
        $rd = array('status'=>-1);
        $applyId = (int)I('applyId',0);
        if($applyId==0)return $rd;
        $m = M('Apply');
        $m->backStatus = (int)I('backStatus');
        $rs = $m->where("applyId = ".$applyId)->save();
        if(false !== $rs){
            $rd['status']= 1;
        }
        return $rd;
    }

    //批量修改提现状态
    public function changeBackStatus(){
        $backStatus = (int)I('status');
        $page = (int)I('page',0);
        $activeId = (int)I('activeId',0);
        if($activeId == 0){return array('status'=>-1);}
        $activePhase = M('Active')->where('id = '.$activeId)->getField('activePhase');
        $lockStatus = M('Active')->where('activePhase = '.$activePhase)->getField('lockStatus');
        if(1 == $lockStatus){return array('status'=>-2);}
        if(0 == $page){
            $applyId = self::formatIn(",", I('applyId',0));
            $res=M('Apply')->where("applyId in({$applyId}) and isDel = 1")->setField(array('backStatus'=>$backStatus));
        }else if(1 == $page){
            $res=M('Apply')->where("isDel = 1 and activePhase = ".$activePhase)->setField(array('backStatus'=>$backStatus));
        }else if(2 == $page){
            $applyId = implode(',',unserialize(session('searchesCondition')));
            $res=M('Apply')->where("applyId in($applyId) and isDel = 1")->setField(array('backStatus'=>$backStatus));
        }
        if($res!=false){
            return  array('status'=>0);
        }else{
            return  array('status'=>-1);
        }
    }

    //获取申请信息
    public function getDetails(){
        $applyId = (int)I('applyId',0);
        if($applyId == 0){return false;}
        $info = $this->where('applyId = '.$applyId)->find();
        return $info;
    }

    //修改备注
    public function editRemark(){
        $rd = array('status',-1);
        $id = (int)I('id',0);
        if($id == 0){return $rd;}
        $content = I('content');
        $m = M('Apply');
        $rs = $m->where('applyId = '.$id)->setField(array('remark'=>$content));
        if($rs){
            $rd['status'] = 1;
        }
        return $rd;
    }

    //查看申请商品
    public function view(){
        $id = (int)I('id',0);
        if($id == 0){return false;}
        $m = M('Apply');
        $applyData = $m->where('applyId = '.$id)->find();
        $goodsScore = json_decode($applyData['goodsScore']);
        $goodsModel = M('GoodsPlatform');
        $goodsData = array();
        foreach($goodsScore as $k=>$v){
            $goods = array();
			$arrDate = explode('-',$v);
            $goods['goodsId'] = $arrDate[0];
            $goods['score'] = $arrDate[1];
            $goods['proportion'] = $arrDate[2];
            $goods['goodsName'] = $goodsModel->where('goodsId = '.$goods['goodsId'])->group('goodsId')->getField('goodsName');
            $goodsData['goods'][$k] = $goods;
        }
        //活动期间，获取该会员该期上一次申请记录；活动结束，获取该会员改期最后一次申请记录
        $activePhase = $applyData['activePhase'];
        if(($applyData['dataType'] > 0) && ($applyData['dataType'] != 9999)){
            $dataType = $applyData['dataType']-1;
            $lastApplyData = $m->where('isDel = 1 and activePhase = '.$activePhase.' and dataType = '.$dataType.' and userId = '.$applyData['userId'])->field('goodsScore')->find();
        }else if($applyData['dataType'] = 9999){
            $lastApplyData = $m->where('isDel = 1 and activePhase = '.$activePhase.' and userId = '.$applyData['userId'].' and dataType > 0 and dataType != 9999')->order('applyId desc')->limit('0,1')->field('goodsScore')->find();
        }

        foreach($goodsData['goods'] as $k=>$v){
            $goodsScore = json_decode($lastApplyData['goodsScore']);
            foreach($goodsScore as $ks=>$vs){
                $goods = explode('-',$vs);
                if($v['goodsId'] == $goods[0]){
                    $lastApplyGoodScore = $goods[1];
                }
            }
            $activeScore = $v['score'] - $lastApplyGoodScore;
            $goodsData['goods'][$k]['activeScore'] = sprintf('%.2f',$activeScore);
            $goodsData['goods'][$k]['score'] = sprintf('%.2f',$v['score']);
            //返现金额
            $goodsData['goods'][$k]['cashBack'] = $activeScore*$v['proportion'];
            $goodsData['goods'][$k]['proportion'] = sprintf('%.2f',$v['proportion']*100).'%';
        }
        $goodsData['totalScore'] = $applyData['totalScore'];
        $goodsData['cashBack'] = $applyData['cashBack'];
        return $goodsData;
    }

    //获取所有账号每期最后申请
    public function getUsers(){
        $m = M('Apply');
        $activePhase = (int)I('activePhase',0);
        $sql = "select a.*,u.loginName from __PREFIX__apply a left join __PREFIX__users u on u.userId = a.userId where a.isDel = 1 and a.dataType = 9999";
        if($activePhase>0){$sql .= " and a.activePhase = ".$activePhase;}
        $sql .= "  order by a.activePhase,a.userId,a.dataType";
        $page = $m->pageQuery($sql);
        return $page;
    }

    //获取所有商品
    public function getAllGoods(){
        $m = M('GoodsPlatform');
        $goods = $m->where('isDel = 1')->group('goodsId')->field('goodsName,goodsId')->order('goods_sort,id asc')->select();
        return $goods;
    }

    //手写上传数据
    public function handImport(){
        $rd = array('status',-1);
        $apply = array();
        $applyGoods = array();
        $allGoods = I('allGoods');
        $time = time();
        //获取会员信息
        $users = array();
        $users['loginName'] = trim(I('loginName'));
        $userModel = M('Users');
        $userData = $userModel->where("loginName = '".$users['loginName']."'")->field('userId,limit_id')->find();
        $userId = $userData['userId'];
        //计算总积分
        $sumScore = 0;
        foreach($allGoods as $k=>$v){
            $goods = explode('_',$v);
            $goodsScore = $goods[1];
            $sumScore += $goodsScore;
        }
        //计算总返现
        $money = 0;
        //获取该会员该期最后一次申请记录
        $activePhase = (int)I('activePhase',0);
        $dataType = (int)I('dataType',0);
        $applyModel = M('Apply');
        $lastApplyData = $applyModel->where('isDel = 1 and activePhase = '.$activePhase.' and dataType > 0 and dataType != 9999'.' and userId = '.$userId)->order('applyId desc')->limit('0,1')->field('applyId,activeTotalScore,dataType,goodsScore')->find();
        //判断会员是否被拉黑
        if($dataType == 1){
            $blacklist = $userModel->where("loginName = '".$users['loginName']."'")->getField('blacklist');
            if($blacklist == 1){return array('status'=>-2);}
        }
        //本次申请总积分=上传总积分-最后一次上传总积分
        $totalScore = $sumScore - $lastApplyData['activeTotalScore'];
        foreach($allGoods as $k=>$v){
            $goods = explode('_',$v);
            $goodsId = $goods[0];
            $pointId = M('GoodsPointsPart')->where('points_value <= '.$totalScore.' and is_show = 1')->order('points_value desc')->getField('id');
            $proportion = M('GoodsPlatform')->where('goodsId = '.$goodsId.' and grading_id = '.$pointId)->getField('points1');
            //本次该商品申请积分=上传商品积分-最后一次上传商品积分
            $goodsScore = json_decode($lastApplyData['goodsScore']);
            foreach($goodsScore as $ks=>$vs){
                $lastGoods = explode('-',$vs);
                if($goodsId == $lastGoods[0]){
                    $lastScore = $lastGoods[1];
                }
            }
            $goodsScore = $goods[1] - $lastScore;
            //积分不能为负数
            if($goodsScore < 0){
                $goodsName = M('GoodsPlatform')->where('goodsId = '.$goodsId)->getField('goodsName');
                $rd['goodsName'] = $goodsName;
                $rd['status'] = -3;
                return $rd;
            }
            $applyGoods[] = $goodsId.'-'.$goods[1].'-'.$proportion;
            $money += $proportion*$goodsScore;
        }
        //会员操作
        $applyLimitModel = M('ApplyLimit');
        if(!$userId){
            $data = array();
            $data['loginName'] = $users['loginName'];
            $data['createTime'] = $time;
            $data['limit_id'] = $applyLimitModel->where('is_del = 0 and limit_flag = 1')->getField('id');
            $userId = $userModel->add($data);
            $userData['limit_id'] = $data['limit_id'];
        }
        //获取返现上限
        $limit_value = $applyLimitModel->where('id = '.$userData['limit_id'].' and is_del = 0')->getField('limit_value');
        //获取累积提现金额
        $sumCashBack = $applyModel->where('userId = '.$userId.' and activePhase = '.$activePhase)->sum('cashBack');
        $money = ($limit_value-$sumCashBack)>$money?$money:($limit_value-$sumCashBack);

        //增加提现申请
        if($userId){
            $apply['userId'] = $userId;
            $apply['activeTotalScore'] = $sumScore;
            $apply['totalScore'] = $totalScore;
            $apply['cashBack'] = $money;
            $apply['createTime'] = $time;
            $apply['activePhase'] = $activePhase;
            $apply['isDel'] = 1;
            if(($dataType == 1) && ($lastApplyData['dataType'] > 0) && ($lastApplyData['dataType'] != 9999) ){
                $apply['dataType'] = $lastApplyData['dataType']+1;
            }else{
                $apply['dataType'] = $dataType;    
            }
            $apply['goodsScore'] = json_encode($applyGoods);
            $applyId = $applyModel->add($apply);
        }
        if($userId && $applyId){
            $this->commit();
            $rd['status'] = 1;
        }else{
            $this->rollback();
        }
        return $rd;
    }

    //返回最近一次上传之前
    public function backLast(){
        $rd = array('status',-1);
        $this->startTrans();
        $applyModel = M('Apply');
        $sql = "select applyId from __PREFIX__apply where isDel = 1 and createTime = (select max(createTime) from __PREFIX__apply where isDel = 1)";
        $applyData = $applyModel->query($sql);
        $data = array();
        foreach($applyData as $k=>$v){
            $data[] = $v['applyId'];
        }
        $applyId = implode(',', $data);
        $rs = $applyModel->where('applyId in ('.$applyId.')')->setField(array('isDel'=>0));
        if($rs){
            $this->commit();
            $rd['status'] = 1;
        }else{
            $this->rollback();
        }
        return $rd;
    }

    //批量删除申请记录
    public function BatchDelete(){
        $rd = array('status',-1);
        $page = (int)I('page',0);
        $activeId = (int)I('activeId',0);
        if($activeId == 0){return array('status'=>-1);}
        $activePhase = M('Active')->where('id = '.$activeId)->getField('activePhase');
        $lockStatus = M('Active')->where('activePhase = '.$activePhase)->getField('lockStatus');
        if(1 == $lockStatus){return array('status'=>-2);}
        if(0 == $page){
            $applyId = self::formatIn(",", I('id',0));
            $res=M('Apply')->where("applyId in({$applyId}) and isDel = 1")->setField(array('applyFlag'=>0));
        }else if(1 == $page){
            $res=M('Apply')->where("isDel = 1 and activePhase = ".$activePhase)->setField(array('applyFlag'=>0));
        }else if(2 == $page){
            $applyId = implode(',',unserialize(session('searchesCondition')));
            $res=M('Apply')->where("applyId in($applyId) and isDel = 1")->setField(array('applyFlag'=>0));
        }
        if($res!=false){
            $rd['status'] = 1;
        }
        return $rd;
    }

    //批量删除申请记录
    public function BatchDeleteActive(){
        $rd = array('status',-1);
        $page = (int)I('page',0);
        $activeId = (int)I('activeId',0);
        if($activeId == 0){return array('status'=>-1);}
        $activePhase = M('Active')->where('id = '.$activeId)->getField('activePhase');
        $lockStatus = M('Active')->where('activePhase = '.$activePhase)->getField('lockStatus');
        if(1 == $lockStatus){return array('status'=>-2);}
        if(0 == $page){
            $applyId = self::formatIn(",", I('id',0));
            $res=M('Apply')->where("applyId in({$applyId}) and isDel = 1")->delete();
        }else if(1 == $page){
            $res=M('Apply')->where("isDel = 1 and activePhase = ".$activePhase)->delete();
        }else if(2 == $page){
            $applyId = implode(',',unserialize(session('searchesCondition')));
            $res=M('Apply')->where("applyId in($applyId) and isDel = 1")->delete();
        }
        if($res!=false){
            $rd['status'] = 1;
        }
        return $rd;
    }
};
?>