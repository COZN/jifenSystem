<?php
namespace Admin\Controller;
/**
 * 返现上限控制器
 */
class ApplyLimitController extends BaseController{
    /**
     * 分页查询返现上限列表
     */
    public function index(){
        $this->isLogin();
        $m = D('ApplyLimit');
        $page = $m->queryByPage();
        $this->checkPrivelege('fxsx_00');
        $pager = new \Think\Page($page['total'],$page['pageSize']);
        $page['pager'] = $pager->show();
        $this->assign('Page',$page);
        $this->display('limitList');
    }
    //跳转到修改返现上限
    public function toEdit(){
        $this->isLogin();
        $m = D('ApplyLimit');
        $id = (int)I('id',0);
        if($id){
            $this->checkPrivelege('fxsx_02');
            $object = $m->get($id);
        }else{
            $this->checkPrivelege('fxsx_01');
            $object = $m->getModel();
        }
        $this->assign('object',$object);
        $this->display('editLimit');
    }
    //修改返现上限
    public function edit(){
        $this->isAjaxLogin();
        $m = D('ApplyLimit');
        $id = (int)I('id',0);
        if($id){
            $this->checkAjaxPrivelege('fxsx_02');
            $rs = $m->edit($id);
        }else{
            $this->checkAjaxPrivelege('fxsx_01');
            $rs = $m->insert();
        }
        $this->ajaxReturn($rs);
    }
    //删除返现上限
    public function del(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxsx_03');
        $m = D('ApplyLimit');
        $rs = $m->del();
        $this->ajaxReturn($rs);
    }
    /**
     * 分页查询返现上限中的用户
     */
    public function userList(){
        $this->isLogin();
        $m = D('ApplyLimit');
        $data = $m->queryUserByPage();
        $page = $data['rs'];
        $this->checkPrivelege('fxsx_04');
        $pager = new \Think\Page($page['total'],$page['pageSize']);
        $page['pager'] = $pager->show();
        $this->assign('Page',$page);
        $this->assign('applyLimit',$data['applyLimit']);
        $this->assign('referer',$_SERVER['HTTP_REFERER']);
        $this->display('userList');
    }
    //跳转到新增用户
    public function toEditUser(){
        $this->isLogin();
        $limit_id = (int)I('limit_id',0);
        $this->assign('limit_id',$limit_id);
        $this->display('editUser');
    }
    //返现上限添加用户
    public function addUser(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxsx_05');
        $m = D('ApplyLimit');
        $rs = $m->addUser();
        $this->ajaxReturn($rs);
    }
    /**
     * 删除黑名单用户
     */
    public function delUser(){
        $this->isAjaxLogin();
        $this->checkAjaxPrivelege('fxsx_06');
        $m = D('Admin/ApplyLimit');
        $rs = $m->delUser();
        $this->ajaxReturn($rs);
    }
};
?>