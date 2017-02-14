<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/10/11
 * Time: 16:21
 */
namespace Admin\Model;


class MemberModel extends BaseModel {
    private $m = '';
    public function __construct(){
        parent::__construct();
        $this->m = M('users');
    }

    /**
     * 获取所有黑名单用户
     * @return mixed
     */
    public function queryByPage(){
        $sql = "select * from __PREFIX__users WHERE blacklist=1";
        return $this->m->pageQuery($sql);
    }
    /**
     * 获取指定对象
     */
    public function get($id){
        return $this->m->where("userId=$id")->find();
    }
    /**
     * 修改黑名单用户
     */
    public function edit(){
        $rd = array('status'=>-1);
        $id = (int)I("id",0);
        $data = array();
        $data['loginName'] = I("loginName");
        $data['createTime'] = time();
        $data["remark"] = I("remark")? I("remark"): '';
        if(is_array($data) && isset($data)){

            $rs = $this->m->where("userId=$id")->save($data);
            if(false != $rs){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 新增黑名单用户
     */
    public function insert(){
        $rd = array('status'=>-1);
        $data = array();
        $loginName = I("loginName");
        $data["loginName"] = I("loginName");
        $data['createTime'] = time();
        $data["remark"] = I("remark");
        $data["blacklist"] = 1;
        $upData["blacklist"] = 1;
        if(is_array($data) && isset($data)){
            $su = $this->m->where("loginName = '$loginName'")->find();
            //echo $this->m->getLastSql();
            if ($su){
                $rs = $this->m->where("loginName='$loginName'")->save($upData);
                //echo $this->m->getLastSql();
            }else{
                $rs = $this->m->add($data);
            }

            if($rs || $rs >-1){
                $rd['status']= 1;
            }
        }
        return $rd;
    }
    /**
     * 黑名单用户
     */
    public function del(){
        $rd = array('status'=>-1);
        $id = (int)I('id');
        $data['blacklist'] = 0;
        $rs = $this->m->where("userId=$id")->save($data);

        if(false !== $rs){
            $rd['status']= 1;
        }
        return $rd;
    }
}