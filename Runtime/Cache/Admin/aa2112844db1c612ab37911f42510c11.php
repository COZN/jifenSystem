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
    <script src="/Public/plugins/plugins/mydate.js"></script>
    <link rel="stylesheet" type="text/css" href="/Public/plugins/webuploader/webuploader.css" />

      <script src="/Public/js/jquery.min.js"></script>
       <script src="/Public/plugins/bootstrap/js/bootstrap.min.js"></script>
      <script src="/Public/plugins/lazyload/jquery.lazyload.min.js?v=1.9.1"></script>
      <script src="/Public/plugins/plugins/plugins.js"></script>
      <script src="/Public/plugins/formValidator/formValidator-4.1.3.js"></script>
        <script src="/Public/plugins/formValidator/formValidatorRegex.js"></script>
        <script src="/Public/js/common.js"></script>
        <script src="/Tpl/Admin/js/head.js"></script>
        <script src="/Tpl/Admin/js/common.js"></script>
        <script src="/Public/plugins/layer/layer.min.js"></script>
      <script type="text/javascript">
        var ThinkPHP = window.Think = {
                "ROOT"   : "",
                "APP"    : "/index.php",
                "PUBLIC" : "/Public",
                "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>",
                "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
                "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
            var domainURL = "<?php echo WSTDomain();?>";
            var publicurl = "/Public";
            var currCityId = "<?php echo ($currArea['areaId']); ?>";
            var currCityName = "<?php echo ($currArea['areaName']); ?>";
            var currDefaultImg = "<?php echo WSTDomain();?>/<?php echo ($CONF['goodsImg']); ?>";
            var wstMallName = "<?php echo ($CONF['mallName']); ?>";
            $(function() {
              $('.lazyImg').lazyload({ effect: "fadeIn",failurelimit : 10,threshold: 200,placeholder:currDefaultImg});
            });
            function cleanCache(){ 
          Plugins.waitTips({title:'信息提示',content:'正在清除缓存，请稍后...'}); 
          $.post("<?php echo U('Admin/Index/cleanAllCache');?>",{},function(data,textStatus){

            var json = WST.toJson(data);
            if(json.status==1)Plugins.setWaitTipsMsg({content:'缓存清除成功!',timeout:1000});
          });
        }
      </script>
      <script src="/Public/js/think.js"></script>
    <style>
    .webuploader-pick{padding:0px 10px;background:#e23e3d;}
    </style>
    <script>
       var uploading = null;
       $(function(){
        //初始化上传类
         var uploader = new WebUploader.Uploader({
              server:"<?php echo U('Admin/Apply/importGoods');?>",pick:'#filePicker',auto:false,chunked:false,timeout:0,threads:1
          });
        //加入队列前删除队列中的文件
        uploader.on('beforeFileQueued',function(){
          var file = uploader.getFile('WU_FILE_0');
          uploader.reset();
        })
        //加入队列后增加文件名称
        uploader.onFileQueued = function( file ) {
            $('#fileName').html(file.name);
        };

        $('#submitBut').click(function(){
          var activePhase = $('#activePhase option:selected').val();
          if(activePhase == -1){
            WST.msg('请选择活动期数!', {time:2000,icon: 5});return;
          }
          var dataType = $('#dataType option:selected').val();
          if(dataType == -1){
            WST.msg('请选择活动类型!', {time:2000,icon: 5});return;
          }
          Plugins.confirm({title:'信息提示',content:'您确定要上传该数据吗?',okText:'确定',cancelText:'取消',okFun:function(){
              Plugins.closeWindow();
          uploader.option('formData',{dir:'applyRecord',activePhase:activePhase,dataType:dataType});
          uploader.upload();
          uploader.on('uploadProgress',function(file,percentage){
                uploading = WST.msg('正在导入数据，请稍后...',{time:0});
          })
          uploader.on('uploadSuccess',function(file,response){
                  layer.close(uploading);
                  var json = WST.toJson(response);
                  if(json.status==1){
                    var time = formatSeconds(json.time)
                    if(json.blackName != ''){
                      var blackName = '<br />黑名单用户：'+json.blackName;
                    }else{
                      var blackName = '';
                    }
                    if(json.wrongName != ''){
                      var wrongName = '<br />数据出错用户：'+json.wrongName;
                    }else{
                      var wrongName = '';
                    }
                      layer.open({
                        title: '系统提示',
                        content: '导入成功!已导入数据'+json.importNum+"条,用时"+time+wrongName+blackName,
                        icon:1,
                        btn:'好的',
                        area:'500px',
                        yes:function(){
                          location.reload();
                        },
                        cancel:function(){
                          location.reload();
                        }
                      }); 
                  }else if(json.status==-2){
                      WST.msg('请选择活动期数或数据类型!', {time:3000,icon: 5});
                      location.reload();
                  }else{
                      WST.msg('导入数据失败,出错原因：'+json.msg, {icon: 5});
                  }
          })
          uploader.on('uploadError',function(file,reason){
                  layer.close(uploading);
                      layer.open({
                        title: '系统提示',
                        content: '导入成功!,但是出现了未知情况!',
                        icon:1,
                        btn:'好的',
                        yes:function(){
                          location.reload();
                        },
                        cancel:function(){
                          location.reload();
                        }
                      }); 
          })
         }});
        });
       });
        function formatSeconds(value) {
            var theTime = parseInt(value);// 秒
            var theTime1 = 0;// 分
            if(theTime > 60) {
                theTime1 = parseInt(theTime/60);
                theTime = parseInt(theTime%60);
            }
                var result = ""+parseInt(theTime)+"秒";
                if(theTime1 > 0) {
                result = ""+parseInt(theTime1)+"分"+result;
                }
            return result;
        }
    </script>
    <script>
      function backLast(){
      Plugins.confirm({title:'信息提示',content:'您确定要返回最近一次上传之前吗?(此操作不可逆)',okText:'确定',cancelText:'取消',okFun:function(){
       Plugins.closeWindow();
       Plugins.waitTips({title:'信息提示',content:'正在操作，请稍后...'});
       $.post("<?php echo U('Admin/Apply/backLast');?>",function(data,textStatus){
          var json = WST.toJson(data);console.log(json);
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
</head>
<body class='wst-page'>
    <div class="wst-body"> 
       <div class='wst-page-content'>
        <table class="table table-hover table-striped table-bordered wst-form">
           <tr>
             <td colspan='6' style='color:#707070;padding-left:25px;padding-top:5px;'>
             • 请每次只上传一张Excel表，否则只能上传最后一张Excel表<br/>
             • 请保证导入的数据在Excel的第一个工作表(Sheet)<br/>
             • 若Excel上某一行第一列为空则代表商品数据导入完毕<br/>
             <!--• 若没有数据模板，请点击<a href='/Public/template/_goods.xls' style='color:blue;' target='_blank'>下载Excel模板</a></a><br/>-->
             • 推荐使用谷歌浏览器或者火狐浏览器Firefox以获得更佳体验
             </td>
           </tr>
           <tr>
              <th align='right' width='120' height="50">选择活动期数：</th>
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
             <th align='right' width='120' height="50">选择数据类型：</th>
             <td>
                  <select id="dataType">
                  <option value="1">活动期间</option>
                  <option value="9999">活动结束</option>
                </select>   
             </td>
             </tr>
             <tr>
             <th align='right' width='110' height="50">申请返现数据：</th>
             <td>
                 <div id="filePicker" style='margin-left:0px;'>导入申请返现数据</div>     
                 <div style="position: absolute;margin-top: -25px;margin-left: 150px;"><span style="font-weight: bold;">待上传文件名：</span><span id="fileName"></span></div> 
             </td>
           </tr>
           <tr>
           <td colspan="3" align="center">
            <button class="btn btn-success" id="submitBut">上传</button>
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
       </div>
   </div>
   <script type="text/javascript" src="/Public/plugins/webuploader/webuploader.js"></script>
</body>
</html>