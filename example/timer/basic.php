<?php
/**
 * 毫秒定时器
 *
 * User: renxiaogang
 * Date: 2014/12/31
 * Time: 14:47
 */

echo "程序开始运行" . date("H:i:s") . PHP_EOL;

//增加定时器
swoole_timer_add(3000, function($interval) {
    echo "timer[$interval] :".date("H:i:s")." call\n";
});

//只执行一次
swoole_timer_after(5000, function(){
    echo "5秒后执行" . date("H:i:s") . PHP_EOL;
});