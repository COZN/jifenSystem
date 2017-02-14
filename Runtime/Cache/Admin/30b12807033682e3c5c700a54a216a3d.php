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
      <style type="text/css">
		#preview{border:1px solid #cccccc; background:#CCC;color:#fff; padding:5px; display:none; position:absolute;}
	  </style>
   </head>
   <script>
   function checkAll(obj){
      $('.chk').each(function(){
        $(this)[0].checked = obj;
      })
    }
   function del(id){
	   Plugins.confirm({title:'信息提示',content:'您确定要删除该商品吗?',okText:'确定',cancelText:'取消',okFun:function(){
		   Plugins.closeWindow();
		   Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
		   $.post("<?php echo U('Admin/GoodsPlatform/delGoods');?>",{id:id},function(data,textStatus){
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
   //批量删除
   function BatchDelete()
    {
        var params = {};
        params.id = '';
        $('input[num="list"]:checked').each(function(i,item){
            params.id += $(this).attr('id')+',';
        });
        Plugins.confirm({title:'信息提示',content:'您确定要删除这些平台商品吗?',okText:'确定',cancelText:'取消',okFun:function(){
            Plugins.closeWindow();
            Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
            $.post("<?php echo U('Admin/GoodsPlatform/deletes');?>",params,function(data,textStatus){
                var json = WST.toJson(data);console.log(json);
                if(json.status=='1'){
                    Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                        location.reload();
                    }});
                }else{
                    Plugins.closeWindow();
                    parent.showMsg({msg:'操作失败!',status:'danger'});
                }
            });
        }});
    }
  //批量同步
   function BatchSynchro(status)
    {
        var params = {};
        params.id = '';
        $('input[num="list"]:checked').each(function(i,item){
            params.id += $(this).attr('id')+',';
        });
        params.status = status;
        Plugins.confirm({title:'信息提示',content:'您确定要操作这些平台商品吗?',okText:'确定',cancelText:'取消',okFun:function(){
            Plugins.closeWindow();
            Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
            $.post("<?php echo U('Admin/GoodsPlatform/synchros');?>",params,function(data,textStatus){
                var json = WST.toJson(data);console.log(json);
                if(json.status=='1'){
                    Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                        location.reload();
                    }});
                }else{
                    Plugins.closeWindow();
                    parent.showMsg({msg:'操作失败!',status:'danger'});
                }
            });
        }});
    }
    function toggleIsShow(t,v){
       Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
       $.post("<?php echo U('Admin/GoodsPlatform/editiIsSynchro');?>",{id:v,isShow:t},function(data,textStatus){
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
   $.fn.imagePreview = function(options){
		var defaults = {}; 
		var opts = $.extend(defaults, options);
		var t = this;
		xOffset = 5;
		yOffset = 20;
		if(!$('#preview')[0])$("body").append("<div id='preview'><img width='200' src=''/></div>");
		$(this).hover(function(e){
			   $('#preview img').attr('src',"/"+$(this).attr('img'));      
			   $("#preview").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px").show();      
		  },
		  function(){
			$("#preview").hide();
		}); 
		$(this).mousemove(function(e){
			   $("#preview").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px");
		});
	}

   /*$(document).ready(function(){
	    $('.imgPreview').imagePreview();
	    <?php if(!empty($areaId1)): ?>getAreaList("areaId2",'<?php echo ($areaId1); ?>',0,'<?php echo ($areaId2); ?>');<?php endif; ?>
		<?php if($goodsCatId1 != 0 ): ?>getCatList("goodsCatId2",<?php echo ($goodsCatId1); ?>,0,<?php echo ($goodsCatId2); ?>);<?php endif; ?>
		<?php if($goodsCatId2 != 0 ): ?>getCatList("goodsCatId3",<?php echo ($goodsCatId2); ?>,1,<?php echo ($goodsCatId3); ?>);<?php endif; ?>
   });*/
   </script>
   <body class='wst-page'>
    <form method='post' action='<?php echo U("Admin/GoodsPlatform/goods");?>' >
       <div class='wst-tbar'> 
   </div>
   <div class='wst-tbar'>            
       <!--商品分类：
                    <select id='shopCatId1' name='shopCatId1' autocomplete="off"  style="margin-right: 20px;">
                        <option value='0' 
                            <?php if($data["isHandle"] == -1 ): ?>selected<?php endif; ?>
                            >全部
                        </option>
                        <?php if(is_array($shopCatId1)): $i = 0; $__LIST__ = $shopCatId1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo['catId']); ?>' <?php if($shopCatId == $vo['catId'] ): ?>selected<?php endif; ?>
				           onclick='javascript:queryPlatformByPage()'><?php echo ($vo['catName']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>

                    </select>  -->
       商品名称：<input type='text' id='goodsName' name='goodsName' value='<?php echo ($goodsName); ?>'/> 
       <!--商品编号：<input type='text' id='goodsSn' name='goodsSn' value='<?php echo ($goodsSn); ?>'/> -->
  <button type="submit" class="btn btn-primary glyphicon glyphicon-search">查询</button>
  <a class="btn btn-success glyphicon glyphicon-plus" href="/index.php?m=Admin&amp;c=GoodsPlatform&amp;a=toAdd" style="float:right">新增</a>
   </div>
       </form>
       <div class='wst-body'>
        <table class="table table-hover table-striped table-bordered wst-list">
           <thead>
             <tr>
              <th width='30' style="text-align: center">序号</th>
               <th width='180'>商品名称</th>
               <!--<th width='60'>商品编号</th>-->
               <!--<th width='60' style="text-align:center">200万以上</th>
               <th width='60' style="text-align:center">500分以上</th>-->
                 <?php if(is_array($catList)): $i = 0; $__LIST__ = $catList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th width="60" style="text-align: center"><?php echo ($vo['points_name']); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                 <?php $count = count($catList)?>
               <!--<th width='60' style="text-align:center">价格</th>-->
               <!--<th width='25'>销量</th>-->
			 	<!--<th width='60'>同步到商户</th>-->
               <th width='80' style="text-align: center">操作</th>
             </tr>
           </thead>
           <tbody>
            <?php if(is_array($Page['root'])): $k = 0; $__LIST__ = $Page['root'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr >
              <td style="text-align: center">
                  <!--<input type='checkbox' class='chk' name="id[]" num="list" id='<?php echo ($vo['goodsId']); ?>'/>-->
                  <?php echo ($k); ?>
              </td>
               <td img='' class='imgPreview'>
               <!--<img src='/<?php echo ($vo['goodsThums']); ?>' width='50'/>-->
               <?php echo ($vo['goodsName']); ?>
               </td>
               <?php for($i=0;$i<$count;$i++){ $ii=$i+1; echo "<td style='text-align:center'>",$vo["p"][$i]*100,"%</td>"; } ?>
                        <!--<td style="text-align: center"><?php echo $count; ?></td>-->


               <!--<td style="text-align: center"><?php echo ($vo['shopPrice']); ?>&nbsp;</td>-->
               <!--<td>
               <div class="dropdown">
               <?php if($vo['isSynchro']==0 ): ?><button class="btn btn-danger dropdown-toggle wst-btn-dropdown"  type="button" data-toggle="dropdown">
					     不同步
					  <span class="caret"></span>
				   </button>
               <?php else: ?>
                   <button class="btn btn-success dropdown-toggle wst-btn-dropdown" type="button" data-toggle="dropdown">
					     同步
					  <span class="caret"></span>
				   </button><?php endif; ?>
                   <ul class="dropdown-menu" role="menu">
					  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(1,<?php echo ($vo['goodsId']); ?>)">同步</a></li>
					  <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleIsShow(0,<?php echo ($vo['goodsId']); ?>)">不同步</a></li>
				   </ul>
               </div>
               </td>-->
               <td style="text-align: center">
              
              
              <a class="btn btn-default glyphicon glyphicon-pencil" href="<?php echo U('Admin/GoodsPlatform/toAdd',array('id'=>$vo['goodsId']));?>">修改</a>&nbsp;
	          
	               <a class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo['goodsId']); ?>,0)">刪除</a>
	           
	               </td>
               
               </td>
             </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              <tr>
           <!-- <td colspan='8'>
                <input type='checkbox' name='chk' onclick='javascript:checkAll(this.checked)'/>全选
                <input type="button" onclick="javascript:BatchDelete()" value="批量删除"/>
                <input type="button" onclick="javascript:BatchSynchro(1)" value="批量同步"/>
                <input type="button" onclick="javascript:BatchSynchro(0)" value="批量不同步"/>
            </td>-->
        </tr>
             <tr>
                <td colspan='10' align='center'><?php echo ($Page['pager']); ?></td>
             </tr>
           </tbody>
        </table>
       </div>
   </body>
</html>