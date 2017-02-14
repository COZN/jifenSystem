<?php
 namespace Admin\Model;

class ActiveModel extends BaseModel {
    //获取所有活动
    public function queryByPage(){
        $m = M('Active');
        $sql = "select * from __PREFIX__active order by activePhase desc";
        $rs = $m->pageQuery($sql);
        return $rs;
    }

    //获取所有未锁定活动
    public function getAllActiveNotLock(){
        $m = M('Active');
        $sql = "select activePhase from __PREFIX__active where lockStatus = 0 order by activePhase desc";
        $rs = $m->query($sql);
        return $rs;
    }

    //获取活动详情
    public function get($id){
        $m = M('Active');
        $rs = $m->where('id = '.$id)->find();
        $rs['startTime'] = date('Y-m-d H:i:s',$rs['startTime']);
        $rs['endTime'] = date('Y-m-d H:i:s',$rs['endTime']);
        return $rs;
    }

    //增加活动
    public function insert(){
        $rd = array('status'=>-1);
        $m = M('Active');
        $data = array();
        $data['startTime'] = strtoTime(I('startTime'));
        $data['endTime'] = strtoTime(I('endTime'));
        $data['activeRemark'] = I('content');
        $data['activePhase'] = (int)I('activePhase');
        $data['createTime'] = time();
        //开始时间不能小于上期结束时间
        $lastPhase = $data['activePhase']-1;
        $lastEndTime = $m->where('activePhase = '.$lastPhase)->order('activePhase desc')->limit('0,1')->getField('endTime');
        $lastEndTime = $lastEndTime?$lastEndTime:0;
        if($data['startTime'] < $lastEndTime){
            $rd['status'] = -2;
        }else{
            $rs = $m->add($data);
            if($rs){
                $rd['status'] = 1;
            }
        }
        return $rd;
    }

    //修改活动
    public function edit($id){
        $rd = array('status'=>-1);
        $m = M('Active');
        $data['id'] = $id;
        $data['startTime'] = strtoTime(I('startTime'));
        $data['endTime'] = strtoTime(I('endTime'));
        $data['activeRemark'] = I('content');
        $data['activePhase'] = (int)I('activePhase');
        //开始时间不能小于上期结束时间
        $lastPhase = $data['activePhase']-1;
        $lastEndTime = $m->where('activePhase = '.$lastPhase)->order('activePhase desc')->limit('0,1')->getField('endTime');
        $lastEndTime = $lastEndTime?$lastEndTime:0;
        if($data['startTime'] < $lastEndTime){
            $rd['status'] = -2;
        }else{
            $rs = $m->save($data);
            if($rs){
                $rd['status'] = 1;
            }
        }
        return $rd;
    }

    //删除活动
    public function del(){
        $rd = array('status'=>-1);
        $m = M('Active');
        $id = (int)I('id',0);
        if($id == 0){return $rd;}
        //不修改字段，直接删除
        //$m->activeFlag = 0;
        //$rs = $m->where('id = '.$id)->save();
        $activePhase = $m->where('id = '.$id)->getField('activePhase');
        $rs = $m->where('id = '.$id)->delete();
        //删除这期下的所有申请
        if($rs){
            $applyModel = M('Apply');
            $rs1 = $applyModel->where('activePhase = '.$activePhase)->delete();
            $rd['status'] = 1;
        }
        return $rd;
    }

    //锁定活动
    public function lock(){
        $rd = array('status'=>-1);
        $m = M('Active');
        $id = (int)I('id',0);
        $status = (int)I('status',0);
        if($id == 0){return $rd;}
        $rs = $m->where('id = '.$id)->setField(array('lockStatus'=>$status));
        if($rs){
            $rd['status'] = 1;
        }
        return $rd;
    }

    //获取活动锁定状态
    public function getLock(){
        $m = M('Active');
        $activePhase = (int)I('activePhase',0);
        if(0 == $activePhase){return -1;}
        $lockStatus = $m->where('activePhase = '.$activePhase)->getField('lockStatus');
        return $lockStatus;
    }

    //获取期数对应的申请记录
    public function getApply($id){
        set_time_limit(0);
        $backStatus = (int)I('backStatus',-999);
        $loginName = I('loginName');
        $orderType = (int)I('orderType',0);
        $dataType = (int)I('dataType',-999);
        $m = M('Active');
        $activePhase = $m->where('id = '.$id)->getField('activePhase');
        if($id == 0){return false;}
        $m = M('Apply');
        $sql = "select a.*,u.loginName from __PREFIX__apply a left join __PREFIX__users u on u.userId = a.userId where a.isDel = 1 and a.activePhase = ".$activePhase;
        if($backStatus!=-999){$sql .= " and a.backStatus = ".$backStatus;}
        if($loginName!=''){$sql .=" and u.loginName = '".$loginName."'";}
        if($dataType!=-999){$sql .= " and a.dataType = ".$dataType;}
        if(0 == $orderType){
            $sql .= " order by a.createTime desc ";
        }else if(1 == $orderType){
            $sql .= "  order by a.activePhase,a.userId,a.dataType";
        }
        $page = $m->pageQuery($sql);
        $info = $m->query($sql);
        $page['activePhase'] = $activePhase;
        //不分页取出所有查询内容
        $map = array();
        foreach($info as $v){
            $map[] =$v['applyId'];
        }
        //将查询到的id存储到cookie中，方便导出数据
        session('searchesCondition',serialize($map));
        //由于结束dataType=9999,所以获取所有申请次数
        $allDataType = $m->where('activePhase = '.$activePhase.' and isDel = 1')->field('dataType')->select();
        $data = array();
        foreach($allDataType as $k=>$v){
            if(!in_array($v['dataType'],$data) && 9999 != $v['dataType']){
                $data[] = $v['dataType'];
            }
        }
        $page['allDataType'] = $data;
        return $page;
    }

    //根据申请id查询
    public function selectBySelecId($id,$backStatus,$activePhase,$goodsId,$orderType)
    {
        set_time_limit(0);
        $m = M('Apply');
        if(0 == $orderType){
            $order = 'createTime desc';
        }elseif(1 == $orderType){
            $order = 'activePhase,userId,dataType';
        }
        $rs = $m->where("applyId in($id) and isDel = 1 and activePhase = ".$activePhase)->field('applyId,dataType,userId,cashBack,totalScore,activeTotalScore,createTime,remark,backStatus')->order($order)->select();
        //获取每次申请对应商品积分
        $usersModel = M('Users');
        $lastGoodsScore = array();
        foreach($rs as $k=>$v){
            //获取上次申请
            if($v['dataType'] != 9999){
                $lastGoodsScore = $m->where('isDel = 1 and activePhase = '.$activePhase.' and dataType = '.$v['dataType'].'-1 and userId = '.$v['userId'])->limit('0,1')->getField('goodsScore'); 
           }else{
                $lastGoodsScore = $m->where('isDel = 1 and activePhase = '.$activePhase.' and userId = '.$v['userId'].' and dataType > 0 and dataType != 9999')->order('applyId desc')->limit('0,1')->getField('goodsScore');
           }
            $lastGoodsData = json_decode($lastGoodsScore);
            $lastData = array();
            foreach($lastGoodsData as $ks=>$vs){
                 $lastGoods = explode('-',$vs);
                 $lastData[$lastGoods[0]] = $lastGoods[1];
            }
            $goodsScore = $m->where('applyId = '.$v['applyId'])->getField('goodsScore');
            $goodsData = json_decode($goodsScore);
            $data = array();
            $goodsModel = M('GoodsPlatform');
            foreach($goodsId as $ka=>$vs){
                $data[$vs] = 0;
            }
            foreach($goodsData as $ks=>$vs){
                $goods = explode('-',$vs);
                if(in_array($goods[0],$goodsId)){
                    $lastScore = $lastData[$goods[0]];
                    $data[$goods[0]] = $goods['1']-$lastScore;
                }
            }
            $rs[$k] = array_merge($v,$data);
            unset($rs[$k]['applyId']);
            $rs[$k]['userId'] = $usersModel->where('userId = '.$v['userId'])->getField('loginName');
        }
        return $rs;
    }
};
?>