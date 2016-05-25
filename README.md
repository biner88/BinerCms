BinerCms
=============
BinerCms是基于Thinkphp3.2框架开发的内容管理系统，您可以自由扩展其功能。

作者博客：
[http://www.biner.me](http://www.biner.me)

## 配置
导入数据库
```php
base.sql
```
修改数据库配置文件
```php
/Application/Common/Conf/db.php
```

## 帐号
- 超级管理员帐号： super_admin ，密码：admin
- 管理员帐号： admin ，密码：admin

##开发规范
####模块配置
模块下的Config.php文件,如果不需要权限控制可以删除该文件
```php
'name' 模块名称
'description' 模块描述
'icons' 模块图标
'auth' 模块是否需要权限认证 (false|| true)
```
####控制器注释规范
以下//后不需要,包括//，请放在class上一行，不可以放在namespace前面
==请务必按照此格式填写,权限需要！！！,权限需要！！！,权限需要！！！==
```php
/**
 * @name 用户组管理 //必填
 * @auth yes //是否需要认证(false||true)，可以不填
 * @icons users //导航显示的fontawesome图标，可以不填
 * @author biner //作者,可以不填
 * @date 2016-04-13 //开发时间,可以不填
 */
```
需要权限管理的控制器请继承 ```Common\Controller\AdminbaseController``` ，并设置注释@auth为yes，方可生效.
####方法注释规范
以下//后不需要,包括//，请放在该方法上一行
==请务必按照此格式填写，权限需要！！！，权限需要！！！，权限需要！！！==
```php
/**
 * @name 列表 //方法名称 必填
 * @showOnNav yes //是否显示在页面二级导航上 (yes||no) ，选填，默认no
 * @description 该权限可以对用户组进行授权,请谨慎选择 //描述，选填
 */
```
