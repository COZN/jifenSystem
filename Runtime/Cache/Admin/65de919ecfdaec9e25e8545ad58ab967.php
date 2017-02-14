<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>积分返现系统</title>
      <link href="/Public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="/Tpl/Admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />
      <link href="/Tpl/Admin/css/upload.css" rel="stylesheet" type="text/css" />
      <script src="/Public/js/jquery.min.js"></script>
      <script src="/Public/plugins/bootstrap/js/bootstrap.min.js"></script>
      <script src="/Public/js/common.js"></script>
      <script src="/Public/plugins/plugins/plugins.js"></script>
      <script src="/Public/plugins/formValidator/formValidator-4.1.3.js"></script>
      <script type="text/javascript" src="/Tpl/Admin/js/upload.js"></script>
   </head>
   <script>
   var ThinkPHP = window.Think = {
          "ROOT"   : ""
  }
   $(function () {
      
     $.formValidator.initConfig({
       theme:'Default',mode:'AutoTip',formID:"myform",debug:true,submitOnce:true,onSuccess:function(){
           edit();
             return false;
      }});
     $("#loginName").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入会员账号"});
  })
   function edit(){
     var params = {};
     params.activePhase = $("#activePhase option:selected").val();
     if(params.activePhase == '-1'){
      Plugins.Tips({title:'信息提示',icon:'error',content:'请选择活动期数!',timeout:1000});return;
     }
     params.dataType = $("#dataType option:selected").val();
     params.loginName = $('#loginName').val();
     var goodsId = <?php echo json_encode($goodsId);?>;
     var allGoods = new Array();
     for(var p in goodsId){
      var goodsScore = goodsId[p]+'_'+$('#goods_'+goodsId[p]).val();
        allGoods.push(goodsScore); 
     }
     params.allGoods = allGoods;
     Plugins.waitTips({title:'信息提示',content:'正在提交数据，请稍后...'});
        $.post("/index.php?m=Admin&c=Apply&a=handImport",params,function(data,textStatus){
          var json = WST.toJson(data);
          if(json.status=='1'){
            Plugins.setWaitTipsMsg({ content:'操作成功',timeout:1000,callback:function(){
               location.reload();
            }});
          }else if(json.status == -2){
            Plugins.closeWindow();
            Plugins.Tips({title:'信息提示',icon:'error',content:'该会员账号在黑名单中!',timeout:3000});
          }else if(json.status == -3){
            Plugins.closeWindow();
            Plugins.Tips({title:'信息提示',icon:'error',content:'商品"'+json.goodsName+'"数据录入有误，请重新输入!',timeout:3000});
          }else{
            Plugins.closeWindow();
            Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
          }
        });
   }
   var filetypes = ["gif","jpg","png","jpeg"];
   </script>
       <script>
      function backLast(){
      Plugins.confirm({title:'信息提示',content:'您确定要返回最近一次上传之前吗?(此操作不可逆)',okText:'确定',cancelText:'取消',okFun:function(){
       Plugins.closeWindow();
       Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
       $.post("<?php echo U('Admin/Apply/backLast');?>",function(data,textStatus){
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
   <body class="wst-page">
       <form name="myform" method="post" id="myform" autocomplete="off">   
       <input type="hidden" value="<?php echo ($goods); ?>" id="goodsScore" />
        <table class="table table-hover table-striped table-bordered wst-form">
          <tr>
              <th align='right' width='110' height="50">选择活动期数<font color='red'>*</font>：</th>
             <td>
                <select id="activePhase">
                  <?php if($activeNum == 0): ?><option value="<?php echo ($activeData[0]['activePhase']); ?>">第<?php echo ($activeData[0]['activePhase']); ?>期</option>
                  <?php else: ?>
                    <option value="-1">请选择</option>
                    <?php if(is_array($activeData)): $i = 0; $__LIST__ = $activeData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['activePhase']); ?>">第<?php echo ($vo['activePhase']); ?>期</option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </select>    
             </td>
            </tr>
            <tr>
             <th align='right' width='110' height="50">选择数据类型<font color='red'>*</font>：</th>
             <td>
                  <select id="dataType">
                  <option value="1">活动期间</option>
                  <option value="9999">活动结束</option>
                </select>   
             </td>
             </tr>
				<tr>
			   <th width='120'>会员账号<font color='red'>*</font>：</th>
			   <td><input type='text' id='loginName' name='loginName' class="form-control wst-ipt" maxLength='100'/></td>
			</tr>
			
      <tr>
         <th width='120'>商品积分<font color='red'>*</font>：</th>
         <td>
         <?php if(is_array($goods)): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="float:left;width:220px;padding:5px;" >
            <div style="width:110px;float:left;text-align: right;"><?php echo ($vo["goodsName"]); ?>：</div>
            <div style="float:left">
            <input type='text' id='goods_<?php echo ($vo["goodsId"]); ?>' style='width:100px;' class="form-control wst-ipt" value="0" onkeypress="return WST.isNumberdoteKey(event)" onkeyup="javascript:WST.isChinese(this,1)" maxLength='10'/>
            </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
         </td>
      </tr>
             <td colspan='3' style='padding-left:250px;'>
                 <button type="submit" class="btn btn-success">保&nbsp;存</button>
             </td>
           </tr>
           <?php if(in_array('scsq_01',$WST_STAFF['grant'])){ ?>
            <tr>
             <td colspan="3">
            <button class="btn btn-danger" onclick="backLast()">返回最近一次上传之前</button>    
             </td>
            </tr>
            <?php } ?>
        </table>
       </form>
   </body>
</html>