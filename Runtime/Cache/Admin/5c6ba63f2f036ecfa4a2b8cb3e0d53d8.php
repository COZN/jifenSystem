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
     $("#catName").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:20,onError:"商品分类不能超过20个字符"});
   });
   function edit(){
     var params = {};
    /* params.id = $('#id').val();
     params.points_name = $('#points_name').val();*/
     params = $("#myform").serializeArray();
     //params.is_show = $('input[name="is_show"]:checked').val();;
     //console.log(params);
     Plugins.waitTips({title:'信息提示',content:'正在提交数据，请稍后...'});

     $.post("<?php echo U('Admin/GoodsPointsPart/edit');?>",params,function(data,textStatus){
      var json = WST.toJson(data);
      if(json.status=='1'){
        Plugins.setWaitTipsMsg({ content:'操作成功',timeout:1000,callback:function(){
           location.href='<?php echo U("Admin/GoodsPointsPart/pointsPart");?>';
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
             <th width='120' align='right'>积分段名称<font color='red'>*</font>：</th>
             <td><input type='text' name='points_name' class="form-control wst-ipt" value='<?php echo ($object["points_name"]); ?>' maxLength='25'/></td>
           </tr>
           <tr>
               <th width='120' align='right'>积分段值<font color='red'>*</font>：</th>
               <td><input type='text' id='points_value' class="form-control wst-ipt" value='<?php echo ($object["points_value"]); ?>' name="points_value" maxLength='25'/></td>
           </tr>
           <!--<tr>
             <th align='right'>是否显示<font color='red'>*</font>：</th>
             <td>
             <label>
             <input type='radio' id='isShow1' value='1' name='is_show' <?php if($object['is_show'] ==1 ): ?>checked<?php endif; ?>/>显示
             </label>
             <label>
             <input type='radio' id='isShow0' value='0' name='is_show' <?php if($object['is_show'] ==0 ): ?>checked<?php endif; ?>/>隐藏
             </label>
             </td>
           </tr>-->
           <!--<tr>
             <th align='right'>排序号：</th>
             <td><input type='text' id='catSort' class="form-control wst-ipt" value='<?php echo ($object["catSort"]); ?>' style='width:80px' onkeypress="return WST.isNumberKey(event)" onkeyup="javascript:WST.isChinese(this,1)" maxLength='8'/></td>
           </tr>-->
           <tr>
             <td colspan='2' style='padding-left:250px;'>
                 <button type="submit" class="btn btn-success">保&nbsp;存</button>
                  <a class="btn btn-primary" href='<?php echo U("Admin/GoodsPointsPart/pointsPart");?>'>返&nbsp;回</button>
             </td>
           </tr>
        </table>
       </form>
   </body>
</html>