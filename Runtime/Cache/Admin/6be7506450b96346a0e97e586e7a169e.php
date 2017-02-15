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
        Plugins.confirm({title:'信息提示',content:'您确定要删除该用户吗?',okText:'确定',cancelText:'取消',okFun:function(){
            Plugins.closeWindow();
            Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
            $.post("<?php echo U('Admin/ApplyLimit/delUser');?>",{id:id},function(data,textStatus){
                var json = WST.toJson(data);
                if(json.status=='1'){
                    Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                        location.reload();
                    }});
                }else{
                    Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
                }
            });
            return false;
        }});
    }
</script>
<body class='wst-page'>
<div class='wst-tbar' style='height:40px;'>
        积分上限名称：<?php echo ($applyLimit['limit_name']); ?><br />
        积分上限数值：<?php echo ($applyLimit['limit_value']); ?>
    <div style="float:right">
    <?php if(in_array('fxsx_05',$WST_STAFF['grant'])){ ?>
        <a class="btn btn-success glyphicon glyphicon-plus" href="<?php echo U('Admin/ApplyLimit/toEditUser',array('limit_id'=>$applyLimit['id']));?>">新增</a>
        <a class="btn btn-primary" onclick='javascript:location.href="<?php echo ($referer); ?>"'>返回</a>
    <?php } ?>
    </div>
</div>
<div class='wst-body'>
    <table class="table table-hover table-striped table-bordered wst-list">
        <thead>
        <tr>
            <th width="5%">序号</th>
            <th width="20%">会员名称</th>
            <th width='20%'>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($Page['root'])): $i = 0; $__LIST__ = $Page['root'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id='tr_0_<?php echo ($i); ?>' class="tr_0" isLoad='1'>
                <td width="50"><?php echo ($i); ?></td>
                <td><?php echo ($vo['loginName']); ?></td>
                <td>
                    <a class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo['userId']); ?>)">刪除</a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        <tr>
            <td colspan='11' align='center'><?php echo ($List['pager']); ?></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>