<?php
 return array(
	    'VAR_PAGE'=>'p',
	    'PAGE_SIZE'=>100,
		'DB_TYPE'=>'mysql',
	    'DB_HOST'=>'localhost',
	     'DB_NAME'=>'jifen',
	    'DB_USER'=>'root',
	    'DB_PWD'=>'',
	    'DB_PREFIX'=>'oto_',
	    'DEFAULT_CITY' => '440100',
 		'URL_MODEL' => 2,
	    'DATA_CACHE_SUBDIR'=>true,
        'DATA_PATH_LEVEL'=>2, 
	    'SESSION_PREFIX' => 'oto_mall',
        'COOKIE_PREFIX'  => 'oto_mall',
		'LOAD_EXT_CONFIG' => 'ext_config',
 		'MODULE_ALLOW_LIST' => array('Home','Admin','Wx','Go','Api'),    // 允许访问的模块列表
 		//图片上传相关信息
		'VIEW_ROOT_PATH'        =>   '/Upload/',
		/* 自动运行配置 */
	    'CRON_CONFIG_ON' => true, // 是否开启自动运行
	    'CRON_CONFIG' => array(
	        '团购活动倒计时' => array('Home/Base/changeGroupStatus', '1', ''), //路径(格式、间隔秒（0为一直运行）、开始时间
	    ),
	);
?>