<?php
/**
 * Created by PhpStorm.
 * User: 李大榜
 * Date: 2016/10/11
 * Time: 16:12
 */
namespace Admin\Controller;

class MemberController extends BaseController{

    /**
     * 获取所有黑名单用户
     */
    public function index(){
        $this->isLogin();
        $m = D('Admin/Member');
        $page = $m->queryByPage();

        $pager = new \Think\Page($page['total'],$page['pageSize']);// 实例化分页类 传入总记录数和每页显示的记录数
        $page['pager'] = $pager->show();
        $this->assign('List',$page);
        $this->display('/member/list');
    }
    /**
     * 跳到新增/编辑黑名单用户页面
     */
    public function toEdit(){
        $this->isLogin();
        $m = D('Admin/Member');
        $object = array();
        if(I('id',0)>0){
            $this->checkPrivelege('hmd_03');
            $object = $m->get(I('id',0));
        }else{
            $this->checkPrivelege('hmd_02');
            $object = $m->getModel();
        }

        $this->assign('object',$object);
        $this->display('/member/edit');
    }
    /**
     * 新增/修改黑名单用户名操作
     */
    public function edit(){
        $this->isAjaxLogin();
        $m = D('Admin/Member');
        $rs = array();
        if(I('id',0)>0){
            $rs = $m->edit();
        }else{
            $rs = $m->insert();
        }
        $this->ajaxReturn($rs);
    }
    /**
     * 删除黑名单用户
     */
    public function del(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('hmd_04');
        $m = D('Admin/Member');
        $rs = $m->del();

        $this->ajaxReturn($rs);
    }
}