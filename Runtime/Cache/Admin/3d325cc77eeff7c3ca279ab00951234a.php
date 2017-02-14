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
    <script src="/Public/plugins/formValidator/formValidator-4.1.3.js"></script>
    <script src="/Public/js/common.js"></script>
    <script src="/Public/plugins/plugins/plugins.js"></script>
</head>
<script>
    $(function () {
        $.formValidator.initConfig({
            theme:'Default',mode:'AutoTip',formID:"myform",debug:true,submitOnce:true,onSuccess:function(){
                edit();
                return false;
            },onError:function(msg){
            }});
        $("#limit_name").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:20,onError:"请输入返现上限名称"});
        $("#limit_value").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:20,onError:"请输入返现上限数值"});
    });
    function edit(){
        var params = {};
        params = $("#myform").serializeArray();
        Plugins.waitTips({title:'信息提示',content:'正在提交数据，请稍后...'});
        $.post("<?php echo U('Admin/ApplyLimit/edit');?>",params,function(data,textStatus){
            var json = WST.toJson(data);
            if(json.status=='1'){
                Plugins.setWaitTipsMsg({ content:'操作成功',timeout:1000,callback:function(){
                    location.href='<?php echo U("Admin/ApplyLimit/index");?>';
                }});
            }else{
                Plugins.closeWindow();
                Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
            }
        });
    }

</script>
<body class="wst-page">
<form name="myform" method="post" id="myform" autocomplete="off">
    <input type='hidden' name='id' value='<?php echo ($object["id"]); ?>'/>
    <input type='hidden' id='parentId' value='<?php echo ($object["parentId"]); ?>'/>
    <table class="table table-hover table-striped table-bordered wst-form">
        <tr>
            <th width='120' align='right'>返现上限名称<font color='red'>*</font>：</th>
            <td><input type='text' id="limit_name" name='limit_name' class="form-control wst-ipt" value='<?php echo ($object["limit_name"]); ?>' maxLength='25'/></td>
        </tr>
        <tr>
            <th width='120' align='right'>返现上限数值<font color='red'>*</font>：</th>
            <td><input type='text' id='limit_value' name="limit_value" class="form-control wst-ipt" value='<?php echo ($object["limit_value"]); ?>' name="points_value" maxLength='25'/></td>
        </tr>
        <tr>
            <td colspan='2' style='padding-left:250px;'>
                <button type="submit" class="btn btn-success">保&nbsp;存</button>
                <a class="btn btn-primary" href='<?php echo U("Admin/ApplyLimit/index");?>'>返&nbsp;回</button>
            </td>
        </tr>
    </table>
</form>
</body>
</html>