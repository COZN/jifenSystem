<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/10
 * Time: 11:40
 */
namespace  Admin\Model;
class SuggestModel extends BaseModel{
    /**
     * 获取所有的反馈信息
     * @return mixed array
     */
    public function _select()
    {
        $sql = "select u.userName,s.id,s.phone,s.content from __PREFIX__suggest as s left join __PREFIX__users as u on s.userId = u.userId";
        $info = $this->pageQuery($sql);
        return $info;
    }

    /**
     * 根据评论ID删除该评分
     * @param $id  评论ID
     * @return mixed bool
     */
    public function _delete()
    {   
        $rd = array('status',-1);
        $id = (int)I('id');
        $rs = $this->where("id = {$id}")->delete();
        if($rs!=false){
            $rd['status'] = 1;
        }
        return $rd;
    }
}