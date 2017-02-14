<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('ACC_APP', true);
//获取主机
define('HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
//获取上一页地址
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
//获取服务器端口 80 and 443
define('HTTP',isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
//获取域名
define('WEB_URL', HTTP.HTTP_HOST);

// 定义应用目录
define('APP_PATH','./Application/');
//定义加密字符串
define('ENCRYPT',md5(time()));
/* 扩展目录*/
define('EXTEND_PATH', './Library/');
//进入安装目录
if(is_dir("Install") && !file_exists("Install/install.ok")){
	header("Location:Install/index.php");
	exit();
}
// 引入ThinkPHP入口文件
require './ic_core.php';

