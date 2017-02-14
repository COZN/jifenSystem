<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/9/20
 * Time: 16:22
 * 允许登录IP管理控制器
 */
namespace Admin\Controller;

class LogIpController extends BaseController{


    public function index(){
        $this->isLogin();
        $m = D('Admin/LogIp');
        //$list = $m->getCatAndChild();
        $page = $m->queryByPage();
        //p($list);die;
        $pager = new \Think\Page($page['total'],$page['pageSize']);// 实例化分页类 传入总记录数和每页显示的记录数
        $page['pager'] = $pager->show();
        $this->assign('List',$page);
        $this->display("/loglogins/ipList");
    }
    /**
     * 跳到新增/编辑允许登录IP页面
     */
    public function toEdit(){
        $this->isLogin();
        $m = D('Admin/LogIp');
        $object = array();
        if(I('id',0)>0){
            $this->checkPrivelege('dlip_02');
            $object = $m->get(I('id',0));
        }else{
            $this->checkPrivelege('dlip_01');
            $object = $m->getModel();
        }

        $this->assign('object',$object);
        $this->display('/loglogins/edit');
    }
    /**
     * 新增/修改登录IP操作
     */
    public function edit(){
        $this->isAjaxLogin();
        $m = D('Admin/LogIp');
        $rs = array();
        if(I('id',0)>0){
            $rs = $m->edit();

        }else{
            $rs = $m->insert();
        }
        $this->ajaxReturn($rs);
    }
    /**
     * 修改允许登录IP
     */
    public function editName(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('dlip_02');
        $m = D('Admin/LogIp');
        $rs = array('status'=>-1);
        if(I('id',0)>0){
            $rs = $m->editName();
        }
        $this->ajaxReturn($rs);
    }
    /**
     * 删除登录IP操作
     */
    public function del(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('dlip_03');
        $m = D('Admin/LogIp');
        $rs = $m->del();

        $this->ajaxReturn($rs);
    }
}

?>