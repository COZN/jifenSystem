<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ($CONF['mallTitle']); ?>后台管理中心</title>
    <link href="/Public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Tpl/Admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!--[if lt IE 9]>
    <script src="/Public/js/html5shiv.min.js"></script>
    <script src="/Public/js/respond.min.js"></script>
    <![endif]-->
    <script src="/Public/js/jquery.min.js"></script>
    <script src="/Public/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/Public/js/common.js"></script>
    <script src="/Public/plugins/plugins/plugins.js"></script>
</head>
<script>
    function del(id){
        Plugins.confirm({title:'信息提示',content:'您确定要删除该返现上限吗?删除后用户上限将恢复默认值!',okText:'确定',cancelText:'取消',okFun:function(){
            Plugins.closeWindow();
            Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
            $.post("<?php echo U('Admin/ApplyLimit/del');?>",{id:id},function(data,textStatus){
                var json = WST.toJson(data);
                if(json.status=='1'){
                    Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                        location.reload();
                    }});
                }else{
                    Plugins.closeWindow();
                    Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
                }
            });
        }});
    }
</script>
<body class='wst-page'>
<div class='wst-tbar' style='text-align:right;height:25px;'>
    <?php if(in_array('fxsx_01',$WST_STAFF['grant'])){ ?>
    <a class="btn btn-success glyphicon glyphicon-plus"href="<?php echo U('Admin/ApplyLimit/toEdit',array('id'=>$vo['id']));?>" style='float:right'>新增</a>
    <?php } ?>
</div>
<div class="wst-body">
    <table class="table table-hover table-striped table-bordered wst-list">
        <thead>
        <tr>
            <th width='80'>序号</th>
            <th width='200'>返现上限名称</th>
            <th width='200'>返现上限数值(元)</th>
            <th width='300'>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($Page['root'])): $k = 0; $__LIST__ = $Page['root'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr>
                <td><?php echo ($k); ?></td>
                <td><?php echo ($vo['limit_name']); ?></td>
                <td><?php echo ($vo['limit_value']); ?></td>
                <td>
                    <a class="btn btn-default glyphicon glyphicon-pencil" href="<?php echo U('Admin/ApplyLimit/toEdit',array('id'=>$vo['id']));?>">修改</a>
                    <?php if(0 == $vo['limit_flag']): ?><a class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo['id']); ?>)">刪除</a>
                    &nbsp;
                    <a class="btn btn-primary glyphicon glyphicon" href="<?php echo U('Admin/ApplyLimit/userList',array('limit_id'=>$vo['id']));?>">查看用户</a><?php endif; ?>

                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        <tr>
            <td colspan='5' align='center'><?php echo ($Page['pager']); ?></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>