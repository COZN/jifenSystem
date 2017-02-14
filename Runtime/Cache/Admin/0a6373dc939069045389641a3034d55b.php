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
   <body class='wst-page'>
       <div class='wst-tbar' style='height:25px;'>
        <table style="float:left">
          <tr>
            <td>当次总积分：</td>
            <td><?php echo ($Page['totalScore']); ?></td>
          </tr>
            <tr>
            <td>当次返现：</td>
            <td><?php echo ($Page['cashBack']); ?></td>
          </tr>
        </table>
        <button style="float:right;" type="button" class="btn btn-primary" onclick='javascript:location.href="<?php echo ($referer); ?>"'>返&nbsp;回</button>
       </div>
       <div class="wst-body"> 
        <table class="table table-hover table-striped table-bordered wst-list">
           <thead>
             <tr>
               <th width='40'><label>序号</label></th>
               <th width="100">商品名称</th>
               <th width="100">当次积分</th>
               <th width="100">累积积分</th>
               <th width="100">返现比例</th>
               <th width='100'>返现金额</th>
             </tr>
           </thead>
           <tbody>
            <?php if(is_array($Page['goods'])): $i = 0; $__LIST__ = $Page['goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
               <td><?php echo ($i); ?></td>
               <td><?php echo ($vo['goodsName']); ?></td>
               <td><?php echo ($vo['activeScore']); ?></td>
               <td><?php echo ($vo['score']); ?></td>
               <td><?php echo ($vo['proportion']); ?></td>
               <td><?php echo ($vo['cashBack']); ?></td>
             </tr><?php endforeach; endif; else: echo "" ;endif; ?>
           </tbody>
        </table>
       </div>
   </body>
</html>