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
      function del(id){
     Plugins.confirm({title:'信息提示',content:'您确定要删除该活动吗?',okText:'确定',cancelText:'取消',okFun:function(){
       Plugins.closeWindow();
       Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
       $.post("<?php echo U('Admin/Active/del');?>",{id:id},function(data,textStatus){
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

         function lock(id,status){
          var msg = '';
          if(status == 0){
            msg = '您确定要解锁该活动吗?';
          }else{
            msg = '您确定要锁定该活动吗?'
          }
     Plugins.confirm({title:'信息提示',content:msg,okText:'确定',cancelText:'取消',okFun:function(){
       Plugins.closeWindow();
       Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
       $.post("<?php echo U('Admin/Active/lock');?>",{id:id,status:status},function(data,textStatus){
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

       <a class="btn btn-success glyphicon glyphicon-plus" href="<?php echo U('Admin/Active/toEdit');?>" style='float:right'>新增</a>
       
       </div>
       <div class='wst-body'> 
        <table class="table table-hover table-striped table-bordered wst-list">
           <thead>
             <tr>
               <th width='40'>序号</th>
               <th width='60'>活动期数</th>
               <th width='60'>开始时间</th>
               <th width='60'>结束时间</th>	
               <th width="100">备注</th>
               <th width='100'>操作</th>
             </tr>
           </thead>
           <tbody>
            <?php if(is_array($Page['root'])): $i = 0; $__LIST__ = $Page['root'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr >
               <td><?php echo ($i); ?></td>
               <td>第<?php echo ($vo['activePhase']); ?>期</td>
               <td><?php echo date('Y-m-d H:i:s',$vo['startTime']) ?></td>
               <td><?php echo date('Y-m-d H:i:s',$vo['endTime']) ?></td>
               <td><?php echo ($vo['activeRemark']); ?></td>
               <td>
               <?php if($vo['lockStatus'] != 1): ?><a class="btn btn-default glyphicon glyphicon-pencil" href="<?php echo U('Admin/Active/toEdit',array('id'=>$vo['id']));?>">修改</a>&nbsp;<?php endif; ?>
                <?php if($vo['lockStatus'] != 1): ?><button type="button"   class="btn btn-default glyphicon glyphicon-trash" onclick="javascript:del(<?php echo ($vo['id']); ?>)">刪除</button><?php endif; ?>

                <a class="btn btn-primary glyphicon glyphicon" href="<?php echo U('Admin/Active/view',array('id'=>$vo['id']));?>">查看</a>


                <?php if($vo['lockStatus'] == 0): ?><a class="btn btn-danger glyphicon glyphicon" href="#" onclick="javascript:lock(<?php echo ($vo['id']); ?>,1)">锁定</a><?php endif; ?>
                <?php if($vo['lockStatus'] == 1): ?><a class="btn btn-danger glyphicon glyphicon" href="#" onclick="javascript:lock(<?php echo ($vo['id']); ?>,0)">解锁</a><?php endif; ?>

               </td>
             </tr><?php endforeach; endif; else: echo "" ;endif; ?>
             <tr>
                <td colspan='10' align='center'><?php echo ($Page['pager']); ?></td>
             </tr>
           </tbody>
        </table>
       </div>
   </body>
</html>