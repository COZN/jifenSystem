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
   function editName(obj){
	   Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
	  /* var params = {};
	   params.points_name =	$(obj).
	   params.points_value =	$("#points_value").val();*/
	   //console.log($(obj).attr('dataId'));
	   $.post("<?php echo U('Admin/GoodsPointsPart/editName');?>",{id:$(obj).attr('dataId'),parameter:obj.value,name:obj.name},function(data,textStatus){	//{id:$(obj).attr('dataId'),points_value:obj.value}
			var json = WST.toJson(data);
			if(json.status=='1'){
				Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000});
			}else{
				Plugins.closeWindow();
				Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
			}
		});
   }
   function toggleIsShow(t,v){
	   Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
	   $.post("<?php echo U('Admin/GoodsPointsPart/editiIsShow');?>",{id:v,isShow:t},function(data,textStatus){
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
   }
   function del(id){

	   Plugins.confirm({title:'信息提示',content:'您确定要删除该积分段吗?',okText:'确定',cancelText:'取消',okFun:function(){
		   Plugins.closeWindow();
		   Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
		   $.post("<?php echo U('Admin/GoodsPointsPart/del');?>",{id:id},function(data,textStatus){
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
   function loadChildTree(obj,pid,objId){
		var str = objId.split("_");
		level = (str.length-2);
		if($(obj).hasClass('glyphicon-minus')){
			$(obj).removeClass('glyphicon-minus').addClass('glyphicon-plus');
			$('tr[class^="'+objId+'"]').hide();
		}else{
			$(obj).removeClass('glyphicon-plus').addClass('glyphicon-minus');
			$('tr[class^="'+objId+'"]').show();
			$('tr[class^="'+objId+'"] > td >.glyphicon-plus').each(function(){
				$(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');
			})
		}
	}
   </script>
   <body class='wst-page'>
       <div class='wst-tbar' style='text-align:right;height:25px;'>
 
       <a class="btn btn-success glyphicon glyphicon-plus" href="<?php echo U('Admin/GoodsPointsPart/toEdit');?>" style='float:right'>新增</a>
  
       </div>
       <div class='wst-body'> 
        <table class="table table-hover table-striped table-bordered wst-list">
           <thead>
             <tr>
               <th>序号</th>
               <th>积分段名称</th>
				 <th>积分段值</th>
               <!--<th width='80'>积分段值</th>-->
               <!--<th width='150'>是否显示在商户平台</th>-->
               <th width='20%'>操作</th>
             </tr>
           </thead>
           <tbody>
            <?php if(is_array($List)): $i = 0; $__LIST__ = $List;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id='tr_0_<?php echo ($i); ?>' class="tr_0" isLoad='1'>
             <td><?php echo ($i); ?></td>
			 <td>
				<input type='text' value='<?php echo ($vo['points_name']); ?>' onchange='javascript:editName(this)' dataId="<?php echo ($vo["id"]); ?>" name="points_name" class='form-control wst-ipt'/>
			 </td>
			 <td>
				 <input type='text' value='<?php echo ($vo['points_value']); ?>' onchange='javascript:editName(this)' dataId="<?php echo ($vo["id"]); ?>" name="points_value" class='form-control wst-ipt'/>
			 </td>
               <!--<td><?php echo ($vo['catSort']); ?></td>-->
               <!--<td>
               <div class="dropdown">
               <?php if($vo['is_show']==0 ): ?><button class="btn btn-danger dropdown-toggle wst-btn-dropdown"  type="button" data-toggle="dropdown">
					     隐藏
					  <span class="caret"></span>
				   </button>
               <?php else: ?>
                   <button class="btn btn-success dropdown-toggle wst-btn-dropdown" type="button" data-toggle="dropdown">
					     显示
					  <span class="caret"></span>
				   </button><?php endif; ?>
                   <ul class="dropdown-menu" role="menu">
					  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(1,<?php echo ($vo['cid']); ?>)">显示</a></li>
					  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(0,<?php echo ($vo['cid']); ?>)">隐藏</a></li>
				   </ul>
               </div>
               </td>-->
               <td>

               <a class="btn btn-default glyphicon glyphicon-pencil edit" href="<?php echo U('Admin/GoodsPointsPart/toEdit',array('id'=>$vo['id']));?>">修改</a>&nbsp;

               <a class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo['id']); ?>,0)">刪除</a>

               </td>
             </tr>
             <?php if($vo['childNum'] > 0): if(is_array($vo['child'])): $i2 = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i2 % 2 );++$i2;?><tr id='tr_0_<?php echo ($i); ?>_<?php echo ($i2); ?>' class="tr_0_<?php echo ($i); ?>" isLoad='1'>
	               <td>
	               <span class='glyphicon glyphicon-minus' onclick='javascript:loadChildTree(this,<?php echo ($vo2["catId"]); ?>,"tr_0_<?php echo ($i); ?>_<?php echo ($i2); ?>")' style='margin-right:3px;margin-left:20px;cursor:pointer'></span>
	               <input type='text' value='<?php echo ($vo2['catName']); ?>' onchange='javascript:editName(this)' dataId="<?php echo ($vo2["catId"]); ?>" class='form-control wst-ipt'/>
	               </td>
	               <td><?php echo ($vo2['catSort']); ?></td>
	               <td>
	               <div class="dropdown">
	               <?php if($vo2['isShow']==0 ): ?><button class="btn btn-danger dropdown-toggle wst-btn-dropdown"  type="button" data-toggle="dropdown">
						     隐藏
						  <span class="caret"></span>
					   </button>
	               <?php else: ?>
	                   <button class="btn btn-success dropdown-toggle wst-btn-dropdown" type="button" data-toggle="dropdown">
						     显示
						  <span class="caret"></span>
					   </button><?php endif; ?>
	                   <ul class="dropdown-menu" role="menu">
						  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(1,<?php echo ($vo2['catId']); ?>)">显示</a></li>
						  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(0,<?php echo ($vo2['catId']); ?>)">隐藏</a></li>
					   </ul>
	               </div>
	               </td>
	               <td>
	            
	           
	               <a class="btn btn-default glyphicon glyphicon-pencil" href="<?php echo U('Admin/GoodsPointsPart/toEdit',array('id'=>$vo2['id']));?>">修改</a>&nbsp;
	          
	               <a class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo2['catId']); ?>,0)">刪除</a>
	           
	               </td>
	             </tr>
                 <?php if($vo2['childNum'] > 0): if(is_array($vo2['child'])): $i3 = 0; $__LIST__ = $vo2['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo3): $mod = ($i3 % 2 );++$i3;?><tr id='tr_0_<?php echo ($i); ?>_<?php echo ($i2); ?>_<?php echo ($i3); ?>' class="tr_0_<?php echo ($i); ?>_<?php echo ($i2); ?>" isLoad='1'>
		               <td>
		               <span class='glyphicon glyphicon-minus'  style='margin-right:3px;margin-left:40px;cursor:pointer'></span>
		               <input type='text' value='<?php echo ($vo3['catName']); ?>' onchange='javascript:editName(this)' dataId="<?php echo ($vo3["catId"]); ?>" class='form-control wst-ipt'/>
		               </td>
		               <td><?php echo ($vo3['catSort']); ?></td>
		               <td>
		               <div class="dropdown">
		               <?php if($vo3['isShow']==0 ): ?><button class="btn btn-danger dropdown-toggle wst-btn-dropdown"  type="button" data-toggle="dropdown">
							     隐藏
							  <span class="caret"></span>
						   </button>
		               <?php else: ?>
		                   <button class="btn btn-success dropdown-toggle wst-btn-dropdown" type="button" data-toggle="dropdown">
							     显示
							  <span class="caret"></span>
						   </button><?php endif; ?>
		                   <ul class="dropdown-menu" role="menu">
							  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(1,<?php echo ($vo3['catId']); ?>)">显示</a></li>
							  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(0,<?php echo ($vo3['catId']); ?>)">隐藏</a></li>
						   </ul>
		               </div>
		               </td>
		               <td>
		           
		               <a class="btn btn-default glyphicon glyphicon-pencil" href="<?php echo U('Admin/GoodsPlatform/toEdit',array('id'=>$vo3['catId']));?>"">修改</a>&nbsp;
		            
		           
		               <a class="btn btn-default glyphicon glyphicon-trash" href="javascript:del(<?php echo ($vo3['catId']); ?>,0)"">刪除</a>
		           
		               </td>
		             </tr><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
           </tbody>
        </table>
       </div>
   </body>
</html>