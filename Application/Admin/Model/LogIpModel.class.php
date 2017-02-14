<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/9/20
 * Time: 16:17
 * 登录IP管理模型
 */
namespace Admin\Model;


class LogIpModel extends BaseModel {

    /**
     * 获取最大ID值
     * @return mixed
     */
    public function getMaxId(){
        $m = M('LogIp');
        return $m->max('id');
    }
    /**
     * 新增登录IP
     */
    public function insert(){
        $rd = array('status'=>-1);
        $data = array();
        $data["ip"] = I("ip");

        if($this->checkEmpty($data,true)){
            //$data["catSort"] = (int)I("catSort",0);
            //$data["catFlag"] = 1;;
            $m = M('LogIp');

            $rs = $m->add($data);

            if($rs){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 获取已允许登录的Ip
     */
    public function getCatAndChild(){
        $m = M('LogIp');
        $sql = "select * from __PREFIX__log_ip order by id asc";
        $rs1 = $m->query($sql);
        //p($rs1);die;
        return $rs1;
    }
    /**
     * 获取指定对象
     */
    public function get($id){
        $m = M('LogIp');
        return $m->where("id=".(int)$id)->find();
    }
    /**
     * 修改允许登录的IP
     */
    public function edit(){
        $rd = array('status'=>-1);
        $id = (int)I("id",0);
        $data = array();
        $data['ip'] = I("ip");
        if($this->checkEmpty($data)){
            //$data["is_show"] = (int)I("is_show",0);
            $m = M('LogIp');
            $rs = $m->where(" id=".(int)I('id'))->save($data);
            if(false !== $rs){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 修改允许登录IP
     */
    public function editName(){
        $rd = array('status'=>-1);
        $id = (int)I("id",0);
        $data = array();
        $data['ip'] = I('ip');
        //p($data);die;
        if($this->checkEmpty($data)){
            $m = M('LogIp');
            $rs = $m->where("id=".(int)I('id'))->save($data);
            if(false !== $rs){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 删除登录IP
     */
    public function del(){
        $rd = array('status'=>-1);
        $id = (int)I('id');
        $m = M('LogIp');

        $sql = "DELETE FROM __PREFIX__log_ip WHERE id=$id";

        $rs = $m->execute($sql);

        if(false !== $rs){
            $rd['status']= 1;
        }
        return $rd;
    }
    /**
     * 分页列表
     */
    public function queryByPage(){
        $m = M('LogIp');
        $sql = "select * from __PREFIX__log_ip order by id asc";
        return $m->pageQuery($sql);
    }
}

?>