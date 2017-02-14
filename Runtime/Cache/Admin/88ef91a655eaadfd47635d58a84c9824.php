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
      //批量删除记录
     function BatchDelete(t){
          if(-999 == t){return;}
          var ids = [];
          $('.chk').each(function(){
              if($(this).prop('checked'))ids.push($(this).val());
          })
         var activeId = $('#activeId').val();
        Plugins.confirm({title:'信息提示',content:'您确定要删除这些申请记录吗?',okText:'确定',cancelText:'取消',okFun:function(){
            Plugins.closeWindow();
            Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
            $.post("<?php echo U('Admin/Apply/BatchDeleteActive');?>",{id:ids.join(','),page:t,activeId:activeId},function(data,textStatus){
                var json = WST.toJson(data);console.log(json);
                if(json.status=='1'){
                    Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                        location.reload();
                    }});
                }else if(-2 == json.status){
              Plugins.setWaitTipsMsg({title:'信息提示',content:'活动已锁，请先解锁!',timeout:3000,callback:function(){
                  location.reload();
              }});
                }else{
                    Plugins.closeWindow();
                    Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
                }
            });
        }});
    }

   function toggleBackStatus(t,v,activePhase){
     $.post("<?php echo U('Admin/Active/getLock');?>",{activePhase:activePhase},function(data,textStatus){
       var json = WST.toJson(data);
      if(0 == json){
        Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
         $.post("<?php echo U('Admin/Apply/editBackStatus');?>",{applyId:v,backStatus:t},function(data,textStatus){
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
      }else if(1 == json){
              Plugins.Tips({title:'信息提示',content:'活动已锁，请先解锁!',timeout:3000,callback:function(){
                  location.reload();
              }});
      }else{
        Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
      }
    })
   }
    //批量修改提现状态
    function changeBackStatus(t){
      if(-999 == t){return;}
      var ids = [];
      $('.chk').each(function(){
          if($(this).prop('checked'))ids.push($(this).val());
      })
      var page = $('#selectPage option:selected').val();
      if(page == -1){
        Plugins.Tips({title:'信息提示',icon:'error',content:'请先选择处理条件!',timeout:3000});return;
      }
      var activeId = $('#activeId').val();
      var content = "";
      if(t == 0){
        content = "您确定要批量设置兑现状态为'未处理'吗?";
      }else if(t == 1){
        content = "您确定要批量设置兑现状态为'已处理'吗?";
      }else if(t == 2){
        content = "您确定要批量设置兑现状态为'已拒绝'吗?";
      }
      Plugins.confirm({title:'信息提示',content:content,okText:'确定',cancelText:'取消',okFun:function(){
          Plugins.closeWindow();
          Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
          $.post("<?php echo U('Admin/Apply/changeBackStatus');?>",{applyId:ids.join(','),status:t,page:page,activeId:activeId},function(data,textStatus){
              var json = WST.toJson(data);
              if(json.status=='0'){
                  Plugins.setWaitTipsMsg({content:'操作成功',timeout:1000,callback:function(){
                      location.reload();
                  }});
              }else if('-2' == json.status){
                  Plugins.setWaitTipsMsg({content:'该期活动已锁，请先解锁!',timeout:3000,callback:function(){
                      location.reload();
                  }});
              }else{
                  Plugins.closeWindow();
                  Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
              }
          });
      }});
    }
    //导出所选
    function ExportSelected(exportStatus)
    {
      if(-999 == exportStatus){return;}
      var params = {};
      var ids = [];
      $('.chk').each(function(){
          if($(this).prop('checked'))ids.push($(this).val());
      });
      params.applyId = ids.join(',');
      params.activeId = $('#activeId').val();
      params.orderType = $('#orderType option:selected').val();
      //所选
      params.exportStatus = exportStatus;
      var jsonText = JSON.stringify(params);
      
      //$.post("<?php echo U('Admin/Active/outExcel');?>",{applyId:jsonText});
      //location.href="/index.php/Admin/Active/outExcel/applyId/"+jsonText;
      StandardPost("<?php echo U('Admin/Active/outExcel');?>",{applyId:jsonText});
    }

      function  StandardPost(url,args){
            var body = $(document.body),
                form = $("<form method='post'></form>"),
                input;
            form.attr({"action":url});
            $.each(args,function(key,value){
                input = $("<input type='hidden'>");
                input.attr({"name":key});
                input.val(value);
                form.append(input);
            });

            form.appendTo(document.body);
            form.submit();
            document.body.removeChild(form[0]);
        }
      //修改备注
      function remark(applyId,activePhase){
        $.post("<?php echo U('Admin/Active/getLock');?>",{activePhase:activePhase},function(data,textStatus){
          var json = WST.toJson(data);
          if(0 == json){
            location.href="index.php?m=Admin&c=Apply&a=toEditRemark&applyId="+applyId;
          }else if(1 == json){
                  Plugins.Tips({title:'信息提示',content:'该期活动已锁，请先解锁!',timeout:3000,callback:function(){
                      location.reload();
                  }});
          }else{
            Plugins.Tips({title:'信息提示',icon:'error',content:'操作失败!',timeout:1000});
          }
        })
      }
   </script>
   <body class='wst-page'>
     <form method='post' action="<?php echo U('Admin/Active/view');?>">
       <div class='wst-tbar'> 
       <input type="hidden" name="id" value="<?php echo ($id); ?>" id="activeId">
      <span style="font-size:16px;">第<span style="color:red;"><?php echo ($Page['activePhase']); ?></span>期</span>  &nbsp;&nbsp;&nbsp;&nbsp;
       兑现状态：
       <select name="backStatus">
         <option value="-999">请选择</option>
         <option value="0" <?php if($backStatus == 0): ?>selected="selected"<?php endif; ?>>未处理</option>
         <option value="1" <?php if($backStatus == 1): ?>selected="selected"<?php endif; ?>>已处理</option>
         <option value="2" <?php if($backStatus == 2): ?>selected="selected"<?php endif; ?>>已拒绝</option>
       </select>&nbsp;&nbsp;&nbsp;&nbsp;
        次数选择：
       <select name="dataType">
         <option value="-999">请选择</option>
         <option value="9999" <?php if($dataType == 9999): ?>selected="selected"<?php endif; ?>>活动结束</option>
         <?php if(is_array($Page['allDataType'])): $i = 0; $__LIST__ = $Page['allDataType'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo); ?>" <?php if($dataType == $vo): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
       </select>&nbsp;&nbsp;&nbsp;&nbsp;
       排序规则：
       <select name="orderType" id="orderType">
         <option value="0" <?php if($orderType == 0): ?>selected="selected"<?php endif; ?>>时间逆序</option>
         <option value="1" <?php if($orderType == 1): ?>selected="selected"<?php endif; ?>>会员分组</option>
       </select>&nbsp;&nbsp;&nbsp;&nbsp;
       会员账号：<input type='text' id='loginName' name='loginName' class='form-control wst-ipt-10' value='<?php echo ($loginName); ?>'/>
       <button type="submit" class="btn btn-primary glyphicon glyphicon-search">查询</button> 
        <button style="float:right;" type="button" class="btn btn-primary" onclick='javascript:location.href="<?php echo ($referer); ?>"'>返&nbsp;回</button>
       </div>
       </form>
       <div class="wst-body"> 
        <table class="table table-hover table-striped table-bordered wst-list">
           <thead>
             <tr>
               <th width='50'><label><input type='checkbox' name='chk' onclick='javascript:WST.checkChks(this,".chk")'/>序号</label></th>
               <th width="70">期数</th>
               <th width="40">次数</th>
               <th width="110">会员账号</th>
               <th width='100'>返现金额</th>
               <th width='120'>当次总积分</th>
               <th width="120">累积总积分</th>
               <th width="160">上传时间</th>
               <th >备注</th>
               <th width='140'>操作</th>
               <th width='80'>兑现状态</th>
             </tr>
           </thead>
           <tbody>
            <?php if(is_array($Page['root'])): $i = 0; $__LIST__ = $Page['root'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
               <td><input type='checkbox' class='chk' name='chk_<?php echo ($vo['applyId']); ?>' value='<?php echo ($vo['applyId']); ?>'/><?php echo ($i); ?></td>
               <td>第<?php echo ($vo['activePhase']); ?>期</td>
               <td><?php if($vo['dataType'] != 9999): echo ($vo['dataType']); else: ?>结束<?php endif; ?></td>
               
               <td><?php echo ($vo['loginName']); ?></td>
               <td><?php echo ($vo['cashBack']); ?></td>
               <td><?php echo ($vo['totalScore']); ?></td>
               <td><?php echo ($vo['activeTotalScore']); ?></td>
               
               <td><?php echo date('Y-m-d H:i:s',$vo['createTime']); ?></td>
               <td><?php echo ($vo['remark']); ?></td>
               <td>
               <a class="btn btn-primary glyphicon glyphicon" href="<?php echo U('Admin/Apply/view',array('id'=>$vo['applyId']));?>">查看</a>&nbsp;
               <a class="btn btn-default glyphicon glyphicon-pencil" href="javascript:remark(<?php echo ($vo['applyId']); ?>,<?php echo ($vo['activePhase']); ?>)">备注</a>
               </td>
               <td>
               <div class="dropdown">
               <?php if($vo['backStatus']==0 ): ?><button class="btn btn-warning dropdown-toggle wst-btn-dropdown" data="<?php echo ($vo['activePhase']); ?>"  type="button" data-toggle="dropdown">未处理<span class="caret"></span></button>
               <?php elseif($vo['backStatus'] == 1): ?>
                   <button class="btn btn-success dropdown-toggle wst-btn-dropdown" data="<?php echo ($vo['activePhase']); ?>" type="button" data-toggle="dropdown">已处理<span class="caret"></span></button>
               <?php elseif($vo['backStatus'] == 2): ?>
                   <button class="btn btn-danger dropdown-toggle wst-btn-dropdown" data="<?php echo ($vo['activePhase']); ?>" type="button" data-toggle="dropdown">已拒绝<span class="caret"></span></button><?php endif; ?>
                  <ul class="dropdown-menu" role="menu">
                      <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleBackStatus(0,<?php echo ($vo['applyId']); ?>,<?php echo ($vo['activePhase']); ?>)">未处理</a></li>
                      <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleBackStatus(1,<?php echo ($vo['applyId']); ?>,<?php echo ($vo['activePhase']); ?>)">已处理</a></li>
                      <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:toggleBackStatus(2,<?php echo ($vo['applyId']); ?>,<?php echo ($vo['activePhase']); ?>)">已拒绝</a></li>
                  </ul> 
               </div>
               </td>
             </tr><?php endforeach; endif; else: echo "" ;endif; ?>
             <tr>
                <td colspan='15' style="background:none;;">
                    <label><input type='checkbox' name='chk' onclick='javascript:WST.checkChks(this,".chk")'/>全选/取消</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>处理兑现：</b>
                    <select id="selectPage">
                    <option value='-1'>请选择</option>
                    <option value="0">所选</option>
                    <option value="1">全部</option>
                    <option value="2">查询</option>
                    </select>
                    <select onchange="changeBackStatus(this.value)">
                    <option value='-999'>请选择</option>
                    <option value="0">批量未处理</option>
                    <option value="1">批量已处理</option>
                    <option value="2">批量已拒绝</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>导出数据：</b>
                    <select onchange="ExportSelected(this.value)">
                    <option value='-999'>请选择</option>
                    <option value="0">导出所选</option>
                    <option value="1">导出全部</option>
                    <option value="2">导出查询</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>删除记录：</b>
                    <select onchange="BatchDelete(this.value)">
                    <option value='-999'>请选择</option>
                    <?php if(in_array('hdlb_06',$WST_STAFF['grant'])){ ?>
                    <option value="0">删除所选</option>
                    <?php } ?>
                    <?php if(in_array('hdlb_07',$WST_STAFF['grant'])){ ?>
                    <option value="1">删除全部</option>
                    <?php } ?>
                    <?php if(in_array('hdlb_08',$WST_STAFF['grant'])){ ?>
                    <option value="2">删除查询</option>
                    <?php } ?>
                    </select>
                </td>
              </tr>
             <tr>
                <td colspan='15' align='center'><?php echo ($Page['pager']); ?></td>
             </tr>
           </tbody>
        </table>
       </div>
   </body>
</html>