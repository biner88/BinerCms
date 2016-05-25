<?php
return array(
    'LOAD_EXT_CONFIG'     =>'db',
    /* 数据缓存设置 */
    'DATA_CACHE_TIME'     =>  3600,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_PREFIX'   =>  'biner_',     // 缓存前缀
    'DATA_CACHE_TYPE'     =>  'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    //地址规则
    'URL_MODEL'           =>  0, // URL访问模式
    'DEFAULT_MODULE'      => 'Admin',
    'MODULE_ALLOW_LIST'   =>  array('Common','Admin'),
  //	'URL_CASE_INSENSITIVE'	=> false, //默认false 表示URL区分大小写 true则表示不区分大小写
    //开启防止sql注入
    'REQUEST_VARS_FILTER' =>true,
    'ERROR_PAGE'            => '/500.html',	// 错误定向页面
    //静态路径
    'TMPL_PARSE_STRING'   => array(
      '__STATIC__'        =>__ROOT__.'/static', // 静态资源本地访问
  		//'__STATIC_DOMAIN__' =>'http://zimg1.qiniudn.com/statics', // 静态资源远程访问
  		'__STATIC_DOMAIN__' =>__ROOT__.'/static', // 静态资源远程访问
  		'__FILE__'          =>__ROOT__.'/data', // 上传文件路径,本地路径
  		'__FILE_DOMAIN__'   =>__ROOT__.'/data', //上传文件路径,远程访问
    ),
    //后台认证相关
    'USER_AUTH_TYPE'      =>1,                       // 默认认证类型 1 登录认证 2 实时认证
    'SUPER_USER'          =>1,                       // 超级管理员的ID,不验证权限
    'ADMIN_SESSION_KEY'   =>'admin_key', //后台用户session键值
    'VAR_PAGE'            =>'page',
);
