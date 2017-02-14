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
     $("#goodsSn").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入商品编号"});
      $("#goodsName").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:200,onError:"请输入商品名称"});
     $("#marketPrice").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入市场价格"});
     $("#shopPrice").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入店铺价格"});
     $("#goodsStock").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入库存"});
     $("#goodsUnit").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请输入商品单位"});
     $("#catsId").formValidator({onShow:"",onFocus:"",onCorrect:"输入正确"}).inputValidator({min:1,max:50,onError:"请选择商品分类"});
  })
   function edit(){
     var params = {};
     //params.goodsId = $.trim($('#goodsId').val());
     /*params.points1 = $.trim($('#1').val());
     params.points2 = $.trim($('#2').val());
     params.points3 = $.trim($('#3').val());
     params.points4 = $.trim($('#4').val());
     params.points5 = $.trim($('#5').val());*/
     params = $("#myform").serializeArray();
     Plugins.waitTips({title:'信息提示',content:'正在提交数据，请稍后...'});
        $.post("<?php echo U('Admin/GoodsPlatform/add');?>",params,function(data,textStatus){
          var json = WST.toJson(data);
          if(json.status=='1'){
            Plugins.setWaitTipsMsg({ content:'操作成功',timeout:1000,callback:function(){
               location.href='<?php echo U("Admin/GoodsPlatform/goods");?>';
            }});
          }else{
            Plugins.closeWindow();
            Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败！',timeout:1000});
          }
        });
   }
   var filetypes = ["gif","jpg","png","jpeg"];
   </script>
   <body class="wst-page">
        <iframe name="upload" style="display:none"></iframe>
      <form id="uploadform_Filedata" autocomplete="off" style="position:absolute;top:197px;left:750px;z-index:10;" enctype="multipart/form-data" method="POST" target="upload" action="<?php echo U('Home/Shops/uploadPic');?>" >
        <div style="position:relative;">
        <!-- <input id="goodsImg" name="goodsImg" class="form-control wst-ipt" type="text" value="<?php echo ($object["goodsImg"]); ?>" readonly style="margin-right:4px;float:left;margin-left:8px;width:250px;"/>
        <div class="div1">
          <div class="div2">浏览</div>
          <input type="file" class="inputstyle" id="Filedata" name="Filedata" onchange="updfile('Filedata');" >
        </div> -->
        <div style="clear:both;"></div>
        <!-- <div >&nbsp;(格式为 gif, jpg, jpeg, png)</div> -->
        <input type="hidden" name="dir" value="goods">
        <input type="hidden" name="width" value="150">
        <input type="hidden" name="folder" value="Filedata">
        <input type="hidden" name="sfilename" value="Filedata">
        <input type="hidden" name="fname" value="goodsImg">
        <input type="hidden" id="s_Filedata" name="s_Filedata" value="">
        </div>
    </form>
       <form name="myform" method="post" id="myform" autocomplete="off">   
        <!--<input type='hidden' id='goodsThumbs' value='<?php echo ($object["goodsThums"]); ?>'/>-->
        <input type='hidden' name='goodsId' value='<?php echo ($object["goodsId"]); ?>' />
        <table class="table table-hover table-striped table-bordered wst-form">
            <tr>
			   <th width='120'>商品名称<font color='red'>*</font>：</th>
			   <td><input type='text' id='goodsName' name='goodsName' class="form-control wst-ipt" value='<?php echo ($object["goodsName"]); ?>' maxLength='100'/></td>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $ii=$i-1; ?>
                <tr>
                   <th width='120'><?php echo ($vo["points_name"]); ?><font color='red'>*</font>：</th>
                   <td>
                    <input type='text' id='<?php echo ($vo["id"]); ?>' name='id<?php echo ($vo["id"]); ?>' class="form-control wst-ipt" value="<?php echo ($object["p"]["$ii"]); ?>" onkeypress="return WST.isNumberdoteKey(event)" onkeyup="javascript:WST.isChinese(this,1)" maxLength='10'/>
                    <span style="color:brown"> *直接输入比率省略 % 号</span>
                   </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            <tr>
              <th width="120">排序号：</th>
              <td><input type='text' id='goods_sort' name="goods_sort" class="form-control wst-ipt" value='<?php echo ($object["goods_sort"]); ?>' style='width:80px' onkeypress="return WST.isNumberKey(event)" onkeyup="javascript:WST.isChinese(this,1)" maxLength='8'/>
                  <span style="color:brown"> *值越小排序越靠前</span>
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
</html>