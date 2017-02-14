<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title><?php echo ($CONF['shopTitle']['fieldValue']); ?>后台管理中心</title>
      <link href="/Public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="/Tpl/Admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />
      <link href="/Tpl/Admin/css/upload.css" rel="stylesheet" type="text/css" />
      <!--[if lt IE 9]>
      <script src="/Public/js/html5shiv.min.js"></script>
      <script src="/Public/js/respond.min.js"></script>
      <![endif]-->
      <script src="/Public/js/jquery.min.js"></script>
      <script src="/Public/plugins/bootstrap/js/bootstrap.min.js"></script>
      <script src="/Public/js/common.js"></script>
      <script src="/Public/plugins/plugins/plugins.js"></script>
      <script src="/Public/plugins/formValidator/formValidator-4.1.3.js"></script>
      <script src="/Public/plugins/layer/layer.min.js"></script>
        <script type="text/javascript" src="/Tpl/Admin/js/jedate/jedate.js"></script>
	    <style type="text/css">
	        li{list-style-type: none; }
	        .jedaol{padding: 0px;}
	        .botflex{padding: 0px; }
	    </style>
      
   </head>
   <script>
   var ThinkPHP = window.Think = {
	        "ROOT"   : ""
	}
   var filetypes = ["gif","jpg","png","jpeg"];
   $(function () {
	   $.formValidator.initConfig({
		   theme:'Default',mode:'AutoTip',formID:"myform",debug:true,submitOnce:true,onSuccess:function(){
				   edit();
			       return false;
			}});
   });
   function edit(){
	   var params = {};
	   params.startTime = $.trim($('#startTime').val());
	   params.endTime = $.trim($('#endTime').val());
	   params.content = $.trim($('#content').val());
	   params.activePhase = $.trim($('#activePhase').val());
	   params.id = $('#id').val();
	   if(params.startTime == ''){
	   	WST.msg('请选择开始时间',{icon: 5});return; 
	   }
	   if(params.endTime == ''){
	   	WST.msg('请选择结束时间',{icon: 5});return; 
	   }
	   if(params.startTime >= params.endTime){
	   	WST.msg('结束时间必须大于开始时间',{icon:5,time:3000});return;
	   }
	   Plugins.waitTips({title:'信息提示',content:'正在提交数据，请稍后...'});
		$.post("<?php echo U('Admin/Active/edit');?>",params,function(data,textStatus){
			var json = WST.toJson(data);
			if(json.status=='1'){
				Plugins.setWaitTipsMsg({ content:'操作成功',timeout:1000,callback:function(){
				   location.href='<?php echo U("Admin/Active/Index");?>';
				}});
			}else if(json.status=='-2'){
				Plugins.closeWindow();
				Plugins.Tips({title:'信息提示',icon:'error',content:'开始时间不能小于上期活动结束时间',timeout:3000});
			}else{
				Plugins.closeWindow();
				Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
			}
		});
   }
   </script>
   <body class="wst-page">
       <form name="myform" method="post" id="myform" autocomplete="off">
        <input type='hidden' id='id' value='<?php echo ($object["id"]); ?>'/>
        <input type='hidden' id='activePhase' value='<?php echo ($object["activePhase"]); ?>'/>
        <table class="table table-hover table-striped table-bordered wst-form">
           <tr>
             <th width='120' align='right'>期数：</th>
             <td>第<?php echo ($object["activePhase"]); ?>期</td>
           </tr>
           <tr>
             <th align='right'>开始时间<font color='red'>*</font>：</th>
             <td>
             <input class="datainp" id="startTime" name="startTime" type="text" placeholder="开始日期(包含)" value="<?php echo ($object['startTime']); ?>"  readonly style='width:200px;'>(包含)
             </td>
           </tr>
           <tr>
             <th align='right'>结束时间<font color='red'>*</font>：</th>
             <td>
             <input class="datainp" id="endTime" name="endTime" type="text" placeholder="结束日期(不包含)" value="<?php echo ($object['endTime']); ?>"  readonly style='width:200px;'>(不包含)
             </td>
           </tr>
            <tr>
             <th width='120' align='right'>备注：</th>
             <td>
             <textarea style='width:450px;height:100px' id='content' name='content'><?php echo ($object['activeRemark']); ?></textarea>
             </td>
           </tr> 
           <tr>
             <td colspan='3' style='padding-left:250px;'>
                 <button type="submit" class="btn btn-success">保&nbsp;存</button>
                 <button type="button" class="btn btn-primary" onclick='javascript:history.go(-1)'>返&nbsp;回</button>
             </td>
           </tr>
        </table>
       </form>
   </body>
<script type="text/javascript">
  jeDate({
  dateCell:"#startTime",
  format:"YYYY-MM-DD hh:mm:ss",
  isinitVal:false,
  isTime:true, //isClear:false,
  minDate:"2014-09-19 00:00:00",
  isTime:true,
  okfun:function(val){}
  })
  jeDate({
  dateCell:"#endTime",
  format:"YYYY-MM-DD hh:mm:ss",
  isinitVal:false,
  isTime:true, //isClear:false,
  minDate:"2014-09-19 00:00:00",
  isTime:true,
  okfun:function(val){}
  })
</script>
</html>