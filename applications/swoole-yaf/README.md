##**swoole-yaf**
结合PHP的Yaf框架和Swoole扩展的高性能PHP Web框架

##**描述**
底层使用Swoole内置的swoole_http_server提供服务
上层应用使用Yaf框架搭建

##**使用说明**
打开终端
cd swoole-yaf
php server/server.php

打开浏览器，输入http://ip:9501
1、路由：http://192.168.1.248:9501/test   Index：模块 控制器：Index 方法：test
2、获取参数：http://192.168.1.248:9501/index/index/vote/mid/13392/status/1
参数：mid=13392  status=1

##**swoole版本**
swoole-1.7.8+版本

##**yaf版本**
任意stable版本
