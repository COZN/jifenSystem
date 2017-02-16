<?php
namespace Admin\Model;

class ApplyLimitModel extends BaseModel {
    //获取所有返现上限列表
    public function queryByPage(){
        $m = M('ApplyLimit');
        $sql = "select * from __PREFIX__apply_limit where is_del = 0";
        $rs = $m->pageQuery($sql);
        return $rs;
    }
    //增加返现上限
    public function insert(){
        $rd = array('status'=>-1);
        $m = M('ApplyLimit');
        $data = array();
        $data['limit_name'] = I('limit_name');
        $data['limit_value'] = I('limit_value');
        $data['is_del'] = 0;
        $data['limit_flag'] = 0;
        $rs = $m->add($data);
        if($rs){
            $rd['status'] = 1;
        }
        return $rd;
    }

    //获取返现上限详情
    public function get($id){
        $m = M('ApplyLimit');
        $rs = $m->where('id = '.$id)->find();
        return $rs;
    }
    //修改返现上限
    public function edit($id){
        $rd = array('status'=>-1);
        $m = M('ApplyLimit');
        $data['id'] = $id;
        $data['limit_name'] = I('limit_name');
        $data['limit_value'] = I('limit_value');
        $rs = $m->save($data);
        if($rs){
            $rd['status'] = 1;
        }
        return $rd;
    }
    //删除返现上限
    public function del(){
        $rd = array('status'=>-1);
        $m = M('ApplyLimit');
        $id = (int)I('id',0);
        if($id == 0){return $rd;}
        $rs = $m->where('id = '.$id)->setField('is_del',1);
        if($rs){
            //恢复用户返现上限为默认
            $m = M('Users');
            $m->where('limit_id = '.$id)->setField('limit_id',0);
            $rd['status'] = 1;
        }
        return $rd;
    }
    //获取返现上限中的用户
    public function queryUserByPage(){
        $m = M('ApplyLimit');
        $limit_id = (int)I('limit_id',0);
        $sql = "select * from __PREFIX__users where limit_id = ".$limit_id;
        $rs = $m->pageQuery($sql);
        $applyLimit = $m->where('id = '.$limit_id)->find();
        $data['rs'] = $rs;
        $data['applyLimit'] = $applyLimit;
        return $data;
    }
    //添加返现上限用户
    public function addUser(){
        $rd = array('status'=>-1);
        $m = M('Users');
        $data = array();
        $loginName = I("loginName");
        $limit_id = (int)I('limit_id');
        $data['loginName'] = $loginName;
        $data['limit_id'] = $limit_id;
        $data['createTime'] = time();
        $data['blacklist'] = 0;
        $data['remark'] = '';
        if(is_array($data) && isset($data)){
            $su = $m->where("loginName = '$loginName'")->find();
            if ($su){
                $rs = $m->where("loginName='$loginName'")->setField('limit_id',$limit_id);
            }else{
                $rs = $m->add($data);
            }
            if($rs || $rs >-1){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 删除返现上限用户
     */
    public function delUser(){
        $rd = array('status'=>-1);
        $m = M('Users');
        $id = (int)I('id');
        //查找默认返现上限id
        $data['limit_id'] = M('ApplyLimit')->where('is_del = 0 and limit_flag = 1')->getField('id');
        $rs = $m->where("userId=$id")->save($data);
        if(false !== $rs){
            $rd['status']= 1;
        }
        return $rd;
    }
};
?>